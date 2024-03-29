<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Not Foud</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css"> /* Styles of the 404 page of my website. */
        body {
            background: #F5F7F9;
            color: #d3d7de;
            font-family: "Courier new";
            font-size: 18px;
            line-height: 1.5em;
            cursor: default;
        }

        a {
            color: #007bff;
        }

        .code-area {
            position: absolute;
            width: 320px;
            min-width: 320px;
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
        }

        .code-area > span {
            display: block;
        }

        @media screen and (max-width: 320px) {
            .code-area {
                font-size: 5vw;
                min-width: auto;
                width: 95%;
                margin: auto;
                padding: 5px;
                padding-left: 10px;
                line-height: 6.5vw;
            }
        } </style>
</head>
<body>
<div class="code-area"><span style="color: #777;font-style:italic;"> // 404 page not found. </span> <span> <span
                style="color:#d65562;"> if </span> (<span style="color:#4ca8ef;">!</span><span
                style="font-style: italic;color:#bdbdbd;">found</span>) { </span> <span> <span
                style="padding-left: 15px;color:#007bff"> <i style="width: 10px;display:inline-block"></i>throw </span> <span> (<span
                    style="color: #a6a61f">"404 page"</span>); </span> <span style="display:block">}</span> <span
                style="color: #777;font-style:italic;"> // <a href="<?= url(""); ?>">Go home!</a> </span> </span></div>
</body>
</html>