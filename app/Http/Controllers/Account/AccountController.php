<?php


namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('layouts.account.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add_address()
    {
        return view('layouts.account.add_address');
    }

    /**
     * @param $uuid
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit_address($uuid)
    {
        $address = UserAddress::find($uuid);
        if($address !== null && $address->user->id === Auth::user()->id){
            return view('layouts.account.edit_address')->with([
                'address' => Auth::user()->addresses->find($uuid)
            ]);
        }else{
            return redirect(route('account'));
        }

    }

    /**
     * @param Request $request
     * @param $uuid
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function post_edit_address(Request $request, $uuid)
    {
        $request->validate([
            'first_line' => 'required|string',
            'second_line' => 'required|string',
            'city' => 'required|string',
            'postcode' => 'required|string',
            'country' => 'required|string',
        ]);

        $address = UserAddress::find($uuid);
        if($address !== null && $address->user->id === Auth::user()->id){
            $address->first_line = $request->get('first_line');
            $address->second_line = $request->get('second_line');
            $address->city = $request->get('city');
            $address->postcode = $request->get('postcode');
            $address->country = $request->get('country');
            $address->save();

            return redirect(route('account'))->with([
                'alert-success' => 'Thanks for updating your address.'
            ]);
        }else{
            return redirect(route('account'))->withErrors([
                'alert-danger' => 'The address you wanted to update is either invalid or no longer available.'
            ]);
        }
    }

    /**
     * @param Request $request
     * @param $uuid
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function post_delete_address(Request $request, $uuid)
    {
        $address = UserAddress::find($uuid);
        if($address !== null && $address->user->id === Auth::user()->id){
            $address->delete();

            return response(200);
        }else{
            return response(400);
        }
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function post_add_address(Request $request)
    {
        $request->validate([
            'first_line' => 'required|string',
            'second_line' => 'required|string',
            'city' => 'required|string',
            'postcode' => 'required|string',
            'country' => 'required|string',
        ]);

        if($request->get('override') === 'on' || count(Auth::user()->addresses) === 0){

            if($request->get('override') === 'on'){
                $address = Auth::user()->addresses->first();
                if($address !== null){
                    $address->delete();
                }
            }

            $address = new UserAddress();
            $address->user_id = Auth::user()->id;
            $address->first_line = $request->get('first_line');
            $address->second_line = $request->get('second_line');
            $address->city = $request->get('city');
            $address->postcode = $request->get('postcode');
            $address->country = $request->get('country');
            $address->save();

            return redirect(route('account'))->with([
                'alert-success' => 'Thanks for updating your address.'
            ]);
        }else{
            return view('layouts.account.add_address')->withErrors([
                'alert-danger' => 'You can only have one address, please ensure you tick `Replace your current address` in the form to replace your current address.'
            ]);
        }

    }

}