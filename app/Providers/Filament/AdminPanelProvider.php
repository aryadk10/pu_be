<?php

namespace App\Providers\Filament;

use App\Filament\Resources\WidgetsResource\Widgets\PendapatanChart;
use App\Filament\Resources\WidgetsResource\Widgets\PendapatanRetributorChart;
use App\Filament\Resources\WidgetsResource\Widgets\PendapatanUptChart;
use App\Filament\Resources\WidgetsResource\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rupadana\ApiService\ApiServicePlugin;
use Filament\FontProviders\GoogleFontProvider;



class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([

            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                StatsOverview::class,
                PendapatanChart::class,
                PendapatanUptChart::class,
                PendapatanRetributorChart::class
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->colors([
                'danger' => '#C13056',
                'info' => '#DEF1F7',
                'primary' => '#E9A62A',
                'success' => '#24758F',
                'warning' => '#EC8815',
            ])
            ->font('Roboto', provider: GoogleFontProvider::class)
            ->authMiddleware([
                Authenticate::class,
            ])
            ->brandLogo(asset('image/logo.png'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('image/logo.png'))
            ->plugins([
                ApiServicePlugin::make()
            ])
            ->sidebarCollapsibleOnDesktop();
    }
}
