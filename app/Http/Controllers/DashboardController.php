<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ApplicationSetting;
use App\Models\Nft;
use App\Models\Kategori;
use App\Models\lelang;
use App\Models\Komentar;
use App\Models\PenawaranLelang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $teams = ApplicationSetting::all();

        $nftsCount = Nft::count();
        $nftsSold = Nft::where('status', 'sold')->count();
        $nftsAvailable = $nftsCount - $nftsSold;
        $categories = Kategori::count();
        $openLelang = Lelang::where('status', 'open')->count();
        $closedLelang = Lelang::where('status', 'closed')->count();
        $commentsCount = Komentar::count();
        $usersCount = User::count();
        $usersWithNFTs = User::has('nfts')->count();
        $penawaranCount = PenawaranLelang::count();
        $users = User::all();
        $usersWithNFTsData = User::has('nfts')->get();
        $latestComments = Komentar::with('user')->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'teams',
            'nftsCount',
            'nftsSold',
            'nftsAvailable',
            'categories',
            'openLelang',
            'closedLelang',
            'commentsCount',
            'usersCount',
            'usersWithNFTs',
            'users',
            'usersWithNFTsData',
            'latestComments'
        ));
    }


    public function accountSetting(Request $request)
    {
        return view('dashboard.pages.account.setting', [
            'user' => Auth::user(),
        ]);
    }


    public function updateprofile(Request $request, User $user)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $fotoPath = $user->foto;

        if ($request->has('avatar_remove') && $request->avatar_remove == "1") {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $fotoPath = null;
        } elseif ($request->file('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $fotoPath = $request->file('foto')->store('fotos', 'public');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('account_setting')->with('success', 'Profile updated successfully.');
    }
}
