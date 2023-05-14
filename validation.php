<?php

function loginErr()
{
    return "メールアドレスまたはパスワードが不正です";
}

function empNameErr()
{
    return '社員名が未入力です';
}

function mailErr()
{
    return 'メールアドレスが不正です';
}

function pwdErr()
{
    return 'パスワードが不正です';
}

function custNameErr()
{
    return '顧客名が未入力です';
}

function telErr()
{
    return '電話番号が不正です';
}

function genderErr()
{
    return '性別が未選択です';
}

function tantoErr()
{
    return '担当営業が未選択です';
}

function roleErr()
{
    return '役職が未選択です';
}

//htmlspecialchars関数
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}
