<?php

?>

<!DOCTYPE HTML>
<html>
    <head>
        <title><?php echo $anime['mangajap_anime_title']; ?> - Anime | MangaJap</title>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width"/>
        
        <style><?php include($_SERVER['DOCUMENT_ROOT'].'/css/style.css'); ?></style>
        
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    </head>
    
    <body>

        <?php include ($_SERVER['DOCUMENT_ROOT'].'/common/header.php'); ?>

        <div class="container">
            
            <h1><?php echo $anime['mangajap_anime_title']; ?></h1>
            
            <nav>
                <img src=<?php echo $anime['mangajap_anime_cover']; ?> />
            </nav>
            
            <section>
                
                <article>
                    <h2>Statistiques</h2>
                    
                    <p><?php echo $anime['mangajap_anime_score']; ?></p>
                    <p><?php echo $anime['mangajap_anime_ranked']; ?></p>
                    <p><?php echo $anime['mangajap_anime_popularity']; ?></p>
                    <p><?php echo $anime['mangajap_anime_members']; ?></p>
                    <p><?php echo $anime['mangajap_anime_favorites']; ?></p>
                    
                </article>
                
                <article>
                    <h2>Synopsis</h2>
                    
                    <p><?php echo $anime['mangajap_anime_synopsis']; ?></p>
                    
                </article>
                
            </section>
            
        </div>

        <?php include ($_SERVER['DOCUMENT_ROOT'].'/common/footer.php'); ?>

    </body>
</html>

