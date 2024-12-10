<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>{{ _t("Test Gateway") }}</title>
    <style>

        * {
            margin: 0;
            padding: 0;
        }

        a {
            text-decoration: none;
        }

        body {
            background-color: #f1f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;

        }

        p {
            margin-bottom: 45px;
            color: #2a2a2a;
            font-size: 20px;
            font-family: Calibri, serif;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-family: Calibri, serif;
            font-size: 34px;
        }

        .btn {
            box-shadow: none;
            border: none;
            height: 40px;
            padding: 0 15px;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
            background-color: #5098ef;
            display: inline-block;
            line-height: 40px;
            font-family: Calibri, serif;
        }

        .btn:hover {
            background-color: #3e8ae6;
        }

        .btn:focus {
            outline: none
        }

        .btn.fail {
            background: #ef5050;
        }

        .btn.fail:hover {
            background: #cd4545;
        }
    </style>
</head>
<body>
<form dir="rtl" id="form" name="form" method="post">
    @csrf
    <input type="hidden" name="token" value="{{ $payment->transaction_id }}">
    <input type="hidden" name="transaction_id" value="{{ time() }}">
    <input type="hidden" name="status" value="">
    <h2>{{ _t("Test Gateway") }}</h2>
    <p>{{ _t("Please select one of the following options.") }}</p>
    <div style="text-align: center">
        <a id="btn-success" class="btn" onclick="success()">{{ _t("Successful payment") }}</a>
        <a id="btn-failed" class="btn fail" onclick="fail()">{{ _t("Unsuccessful payment") }}</a>
    </div>
</form>
<script>
    function success() {
        document.getElementsByName("status")[0].value = 1;
        document.getElementById('form').submit()
    }

    function fail() {
        document.getElementsByName("status")[0].value = 2;
        document.getElementById('form').submit()
    }
</script>
</body>
</html>
