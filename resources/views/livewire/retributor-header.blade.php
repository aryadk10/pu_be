<div class="flex w-full">
    <div style="width: 80%;">
        {{ $this->form }}
    </div>
    <div style="width: 20%;" class="flex flex-col gap-2 mb-4">
        <a href="{{$this->data['passport_url']}}" target="_blank" class="text-center bg-blue-500 w-full hover:bg-blue-700 border border-[#aaaaaa]-500 font-bold py-2 px-4 rounded-full">
            Foto Paspor
        </a>
        <a href="{{$this->data['ktp_url']}}" target="_blank" class="text-center bg-blue-500 w-full hover:bg-blue-700 border border-[#aaaaaa]-500 font-bold py-2 px-4 rounded-full">
            KTP
        </a>
        <a href="{{$this->data['kk_url']}}" target="_blank" class="text-center bg-blue-500 w-full hover:bg-blue-700 border border-[#aaaaaa]-500 font-bold py-2 px-4 rounded-full">
            KK
        </a>
        <a href="{{$this->data['seritifikat_url']}}" target="_blank" class="text-center bg-blue-500 w-full hover:bg-blue-700 border border-[#aaaaaa]-500 font-bold py-2 px-4 rounded-full">
            Surat Pernyataan
        </a>
    </div>
</div>
