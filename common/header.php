<html>

<head>
    <style type="text/css">
        header {
            background: #ffffff;
        }

        .header-top {
            padding: 10px 0;
            display: flex;
            width: 100%;
        }

        .header-logo {
            height: 120px;
            float: left;
        }

        .header-profile {
            float: right;
            height: 100px;
            display: inline-block;
            box-shadow: 0 0 5px 0 grey;
            margin: auto 0 auto auto;
        }

        .header-profile-pic {
            height: 100px;
            object-fit: cover;
            width: 100px;
            border: 1px solid #000000;
            float: left;
        }

        .header-profile-info {
            padding: 8px 10px;
            width: 280px;
            display: grid;
            float: left;
            height: 100%;
        }

        header .btn {
            text-align: center;
            background: #753951;
            display: inherit;
        }
        header .btn:hover {
            cursor: pointer;
        }

        header .btn span {
            color: #ffffff;
            font-weight: 700;
            margin: auto;
        }

        header #login {
            background: #dc0e0e;
        }
        header #login:hover {
            background: #920a0a;
        }

        header #register {
            background: #373536;
            margin-top: 5px;
        }
        header #register:hover {
            background: #202020;
            cursor: pointer;
        }

        header .pseudo {
            margin: 0;
            padding: 0;
            font-size: 20px;
        }

        header nav {
            background: #202020;
        }

        header .menu {
            color: #fff;
            text-transform: uppercase;
            font-weight: 700;
            padding:0;
            margin:0;
            list-style-type:none;
            word-spacing: -2em;
        }

        header .menu-item {
            display: inline-block;
            border-right: 1px solid #000000;
            text-align: center;
            width: 33.33333%;
        }

        header .menu-item:hover {
            background: #373536;
            cursor: pointer;
        }

        header .menu-item.first {
            border-left: 1px solid #000000;
        }

        header .menu-item a {
            color: #fff;
            display: block;
            margin: 0px;
            padding: 20px;
            text-decoration: none;
        }
    </style>
</head>



<body>

<header>

    <div class="container">

        <div class="header-top">

            <a href="http://mangajap.000webhostapp.com">
                <img class="header-logo"
                     src="http://mangajap.000webhostapp.com/img/logo.png">
            </a>

        </div>

    </div>

    <nav>
        <div class="container">

            <ul class="menu">
                <li class="menu-item first">
                    <a href="http://mangajap.000webhostapp.com">Accueil</a>
                </li>
                <li class="menu-item">
                    <a href="http://mangajap.000webhostapp.com/manga">Manga</a>
                </li>
                <li class="menu-item">
                    <a href="http://mangajap.000webhostapp.com/anime">Anime</a>
                </li>
            </ul>

        </div>
    </nav>

</header>

</body>

</html>