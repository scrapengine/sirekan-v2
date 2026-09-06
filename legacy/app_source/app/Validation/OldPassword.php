<?php

namespace App\Validation;

class OldPassword
{

    public function old_password(string $old_password, string &$error = null)
    {
        //   $old_password = $this->request->getPost('old_password');
        $pass = base64_encode(hash('sha384', $old_password, true));

        //Myth-auth has helper to get field data in database using user()->column_name function
        if (password_verify($pass, user()->password_hash)) {
            return true;
        }
        //pesan jika gagal validasi
        $error = "Password lama salah.";
        return false;
    }
}
