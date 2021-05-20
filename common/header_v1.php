<header>

    <div class="container">

        <div class="header-top">

            <a href="http://mangajap.000webhostapp.com">
                <img class="header-logo"
                     src="http://mangajap.000webhostapp.com/images/logo.png">
            </a>

            <?php if($id_user == 0) : ?>
                <div class="header-profile">
                    <img class="header-profile-pic"
                         src="http://mangajap.000webhostapp.com/images/user/profilepic/0.jpg" />
                    <div class="header-profile-info">
                        <div id="login" class="btn"><span>Se connecter</span></div>
                        <div id="register" class="btn"><span>S'inscrire</span></div>
                    </div>
                </div>
            <?php else : ?>
                <div class="header-profile">

                    <?php createProfilePage($myProfile) ?>

                    <img class="header-profile-pic"
                         src=<?php echo $myProfile["mangajap_user_profilepic"]; ?> />
                    <div class="header-profile-info">
                        <h1 class="pseudo">
                            <a href=<?php print('http://mangajap.000webhostapp.com/profile/'.getValidFileName($myProfile["mangajap_user_pseudo"])); ?>>
                                <?php echo $myProfile["mangajap_user_pseudo"]; ?>
                            </a>
                        </h1>

                        <a id="logout">Se déconnecter</a>
                    </div>
                </div>
            <?php endif; ?>


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



<div id="login_dialog" class="modal">

    <div class="modal-content">

        <h1>Se connecter</h1>

        <form id="login_form" title="" method="post">
            <input type="text" name="pseudo" placeholder="Pseudo ou email" value="" required="" autocomplete="off">
            <input type="password" name="password" placeholder="Mot de passe" value="" required="" autocomplete="off">

            <button type="submit">Se connecter</button>
        </form>
    </div>

</div>

<div id="register_dialog" class="modal">

    <div class="modal-content">

        <h1>S'inscrire</h1>

        <form id="register_form" title="" method="post">
            <input type="text" name="first_name" placeholder="First name" value="" required="" autocomplete="off">
            <input type="text" name="last_name" placeholder="Last name" value="" required="" autocomplete="off">
            <input type="text" name="pseudo" placeholder="Pseudo" value="" required="" autocomplete="off">
            <input type="text" name="email" placeholder="Email" value="" required="" autocomplete="off">
            <input type="password" name="password" placeholder="Mot de passe" value="" required="" autocomplete="off">

            <button type="submit">S'inscrire</button>
        </form>
    </div>

</div>


<?php if($id_user == 0) : ?>
    <script>
        var login = document.getElementById("login");
        var login_dialog = document.getElementById("login_dialog");

        var register = document.getElementById("register");
        var register_dialog = document.getElementById("register_dialog");

        login.onclick = function() {
            login_dialog.style.display = "block";
            login_dialog.style.overflow = "auto";
            document.body.style.overflow = "hidden";
        };

        register.onclick = function() {
            register_dialog.style.display = "block";
            register_dialog.style.overflow = "auto";
            document.body.style.overflow = "hidden";
        };

        window.onclick = function(event) {
            if (event.target == login_dialog) {
                login_dialog.style.display = "none";
                document.body.style.overflow = "auto";
            }
            if (event.target == register_dialog) {
                register_dialog.style.display = "none";
                document.body.style.overflow = "auto";
            }
        };


        $("#login_form").submit(function(e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);

            $.ajax({
                type: "POST",
                url: "http://mangajap.000webhostapp.com/database/triplus-login.php",
                data: form.serialize(), // serializes the form's elements.
                success: function(data) {
                    var result = JSON.parse(data)[0];

                    if (result.error) {
                        alert("error in login");
                    }
                    else {
                        setCookie("id_user", result.id);
                        document.location.reload(true);
                    }
                }
            });
        });


        $("#register_form").submit(function(e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);

            $.ajax({
                type: "POST",
                url: "http://mangajap.000webhostapp.com/database/triplus-register.php",
                data: form.serialize(),
                success: function(data) {
                    var result = JSON.parse(data)[0];

                    if (result.error) {
                        alert("error in register");
                    }
                    else {
                        setCookie("id_user", result.id);
                        document.location.reload(true);
                    }
                }
            });
        });
    </script>
<?php else : ?>
    <script>
        var logout = document.getElementById("logout");

        logout.onclick = function() {
            setCookie("id_user", "0");
            document.location.reload(true);
        }

        window.onclick = function(event) {
            if (event.target == login_dialog) {
                login_dialog.style.display = "none";
            }
        }
    </script>
<?php endif; ?>