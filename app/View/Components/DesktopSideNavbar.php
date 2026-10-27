<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DesktopSideNavbar extends Component
{
    public $navLinks = [];

    public $lastSegment = '';

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $userRole = request()->user()->role;

        switch ($userRole) {
            case 'admin':
                $this->navLinks = [
                    ['icon' => 'bi-person-circle', 'level' => 1, 'label' => 'Manajemen Akun', 'href' => route('akun.menu')],
                    ['icon' => 'bi-book', 'level' => 1, 'label' => 'Manajemen Logbook', 'href' => route('logbook.index')],
                    ['icon' => 'bi-key', 'level' => 1, 'label' => 'Ganti Password', 'href' => route('password.index')],
                ];
                break;
            case 'dokter_magang':
                $this->navLinks = [
                    ['icon' => 'bi-person-circle', 'level' => 1, 'label' => 'Manajemen Akun', 'href' => route('akun.menu')],
                    ['icon' => 'bi-person-vcard', 'level' => 2, 'label' => 'Ubah Data', 'href' => route('akun.ubah-data.edit')],
                    ['icon' => 'bi-key', 'level' => 2, 'label' => 'Ganti Password', 'href' => route('akun.password.index')],
                    ['icon' => 'bi-book', 'level' => 1, 'label' => 'Manajemen Logbook', 'href' => route('logbook.index')],
                ];
                break;
            case 'pembimbing_lapangan':
                $this->navLinks = [
                    ['icon' => 'bi-person-circle', 'level' => 1, 'label' => 'Manajemen Akun', 'href' => route('akun.menu')],
                    ['icon' => 'bi-person-lines-fill', 'level' => 2, 'label' => 'Manajemen Ubah Data', 'href' => route('ubah-data.queue')],
                    ['icon' => 'bi-person-vcard', 'level' => 2, 'label' => 'Ubah Data', 'href' => route('akun.ubah-data.edit')],
                    ['icon' => 'bi-key', 'level' => 2, 'label' => 'Ganti Password', 'href' => route('akun.password.index')],
                    ['icon' => 'bi-book', 'level' => 1, 'label' => 'Manajemen Logbook', 'href' => route('logbook.index')],
                ];
                break;
            default:
                break;
        }
        $this->lastSegment = ucwords(str_replace('-', ' ', last(request()->segments())));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.desktop-side-navbar');
    }
}
