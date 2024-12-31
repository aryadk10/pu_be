<?php

namespace App\Models;

use GuzzleHttp\Client;

class BjbTransactionApi
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.bjb.base_url'),
            'timeout'  => config('services.bjb.timeout'),
        ]);
    }

    /**
     * Generate JSON-RPC headers
     *
     * @return array
     */
    private function generateHeaders(): array
    {
        // Set timezone to UTC and get the current UNIX timestamp
        date_default_timezone_set("UTC");
        $inttime = strval(time() - strtotime("1970-01-01 00:00:00"));

        // Create the value for the signature
        $value = sprintf('%s&%d', config('services.bjb.username'), $inttime);

        // Generate the HMAC SHA256 signature
        $signature = hash_hmac("sha256", $value, config('services.bjb.password'), true);
        $signature64 = base64_encode($signature);

        // Return the headers
        return [
            'userid'    => config('services.bjb.username'),
            'signature' => $signature64,
            'key'       => $inttime,
        ];
    }

    /**
     * Send a set_invoice request to the API
     *
     * @param array $invoiceData
     * @return array
     * @throws \Exception
     */
    public function setInvoice(array $invoiceData): array
    {
        try {
            // Generate headers
            $headers = $this->generateHeaders();

            $invoiceData = [
                'terutang'          => '2300000',        // Total amount due
                'pengurang'         => '0',              // Reduction amount
                'penambah'          => '0',              // Addition amount
                'tgl_skrd'          => '2018-01-23',     // SKRD date
                'denda'             => '0',              // Penalty amount
                'jumlah'            => '2300000',        // Total amount
                'dasar'             => '0',              // Basis for calculation
                'setoran'           => '0',              // Payment made
                'pokok'             => '2300000',        // Principal amount
                'jatuh_tempo'       => '2018-02-22',     // Due date
                'tgl_terima'        => '2018-01-23',     // Received date
                'no_skrd'           => '008',            // SKRD number
                'bunga' => '0',
                'jenis_penerimaan' => '1',
                'jenis' => '1',
                'periode_1'         => '2018-01-23',     // Start period
                'periode_2'         => '2018-01-23',     // End period
                'tarif'             => '1',              // Tax rate
                'status'            => '0',              // Invoice status (e.g., 0 for unpaid, 1 for paid)
            ];


            // Merge the invoice data with additional configuration values
            $payload = array_merge([
                'departemen_kode'   => config('services.bjb.departemen_kode'),
                'departemen_nama'   => config('services.bjb.departemen_nama'),
                'objek_kode'        => config('services.bjb.objek_kode'),
                'objek_nama'        => config('services.bjb.objek_nama'),
                'objek_alamat_1'     => config('services.bjb.objek_alamat1'),
                'objek_alamat_2'     => config('services.bjb.objek_alamat2'),
                'produk_kode'       => config('services.bjb.produk_kode'),
                'produk_nama'       => config('services.bjb.produk_nama'),
                'rekening_kode'     => config('services.bjb.rekening_kode'),
                'rekening_nama'     => config('services.bjb.rekening_nama'),
                'pejabat_nm'        => config('services.bjb.pejabat_nm'),
                'subjek_kode'       => config('services.bjb.subjek_kode'),
                'subjek_nama'       => config('services.bjb.subjek_nama'),
                'subjek_alamat_1'    => config('services.bjb.subjek_alamat1'),
                'subjek_alamat_2'    => config('services.bjb.subjek_alamat2')
            ], $invoiceData);
            // var_dump($payload);die;

            // Send the API request
            $response = $this->client->post('', [
                'json'    => [
                    'jsonrpc' => '2.0',
                    'method'  => 'set_invoice',
                    'id'      => 1,
                    'params'  => ['data' => $payload],
                ],
                'headers' => $headers,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            // var_dump($responseBody);
            //debug rmove it when production
            return [
                'kd_bayar' => '2018102020200693'
            ];
            // die;
            if (!isset($responseBody['result']) || $responseBody['result']['code'] !== 0) {
                throw new \Exception($responseBody['result']['message'] ?? 'API error');
            }

            return $responseBody['result']['data'];
        } catch (\Exception $e) {
            throw new \Exception('Error in setInvoice: ' . $e->getMessage());
        }
    }

    /**
     * Send a create_qris request to the API
     *
     * @param string $kdBayar
     * @return array
     * @throws \Exception
     */
    public function createQris(string $kdBayar): array
    {
        try {
            // Generate headers
            $headers = $this->generateHeaders();

            // Prepare payload
            $payload = [
                'jsonrpc' => '2.0',
                'method'  => 'create_qris',
                'id'      => 1,
                'params'  => [
                    'data' => [
                        'kd_bayar' => $kdBayar,
                        'departemen_kode' => config('services.bjb.departemen_kode'),
                    ],
                ],
            ];

            // Send the API request
            $response = $this->client->post('', [
                'json'    => $payload,
                'headers' => $headers,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            // var_dump($responseBody);die;
            //debug
            return [
                'client_type'      => '3',
                'product_code'     => '30',
                'invoice_no'       => '2200805603',
                'description'      => '554533 Tgl:07/09/2022 Tgl:07/10/2022',
                'customer_name'    => 'BINSAR MANIK',
                'customer_email'   => '-',
                'customer_phone'   => '-',
                'expired_date'     => '2022-10-07 23:59:59',
                'amount'           => 105000,
                'qrcode'           => '00020101021226620017ID.CO.BANKBJB.WWW01189360011030012997590208012997590303UMI51470017ID.CO.BANKBJB.WWW0215ID10221858258850303UMI52049399530336054061050005802ID5920WEB-R PEMKOT CILEGON6007CILEGON61054241162490124QRIS20220907134627026138021008173772760703C02630461B7'
            ];
            // Check for errors in the API response
            if (!isset($responseBody['result']) || $responseBody['result']['code'] !== 0) {
                throw new \Exception($responseBody['result']['message'] ?? 'API error');
            }

            return $responseBody['result']['data'];
        } catch (\Exception $e) {
            throw new \Exception('Error in createQris: ' . $e->getMessage());
        }
    }
}
