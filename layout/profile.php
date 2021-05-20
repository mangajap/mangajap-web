<?php

?>

<!DOCTYPE HTML>
<html>
    <head>
        <title><?php echo $profile['mangajap_user_pseudo']; ?> - Profil | MangaJap</title>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width"/>
        
        <style><?php include($_SERVER['DOCUMENT_ROOT'].'/css/style.css'); ?></style>
        
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    </head>
    
    <body>

        <?php include ($_SERVER['DOCUMENT_ROOT'].'/common/header.php'); ?>

        <div class="container">
            
            <h1><?php echo $profile['mangajap_user_pseudo']; ?></h1>
            
            <nav>
                <img src=<?php echo $profile['mangajap_user_profilepic']; ?> />
                
                <p><?php echo ($profile['mangajap_user_firstname']." ".$profile['mangajap_user_lastname']); ?></p>
                
                <p><?php echo $profile['mangajap_user_mangafollows']; ?></p>
                <p><?php echo $profile['mangajap_user_chaptersread']; ?></p>
                <p><?php echo $profile['mangajap_user_volumesread']; ?></p>
                
            </nav>
            
            <section>
                
                
                
            </section>
            
        </div>

        <?php include ($_SERVER['DOCUMENT_ROOT'].'/common/footer.php'); ?>

    </body>
</html>

