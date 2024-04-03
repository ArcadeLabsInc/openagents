<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use LivewireUI\Modal\ModalComponent;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Login extends ModalComponent
{
    use LivewireAlert;

    #[Validate('required|email')]
    public $email;

    #[Validate('required')]
    public $password;

    public function login()
    {
        $this->validate();

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials)) {
            session()->flash('message');

            $this->alert('success', 'You have successfully logged in!');

            return $this->redirectRoute('chat.intro', navigate: true);
        }

        $this->alert('warning', 'Invalid credentials!');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
