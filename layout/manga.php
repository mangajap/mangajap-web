<?php

?>

<!DOCTYPE HTML>
<html>
    <head>
        <title><?php echo $manga['mangajap_manga_title']; ?> - Manga | MangaJap</title>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width"/>
        
        <style><?php include($_SERVER['DOCUMENT_ROOT'].'/css/style.css'); ?></style>
        
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    </head>
    
    <body>

        <?php include ($_SERVER['DOCUMENT_ROOT'].'/common/header.php'); ?>

        <div class="container">
            
            <h1><?php echo $manga['mangajap_manga_title']; ?></h1>
            
            <nav>
                <img src=<?php echo $manga['mangajap_manga_cover']; ?> />
                <img src=<?php echo $manga['mangajap_manga_banner']; ?> />
            </nav>
            
            <section>
                
                <article>
                    <h2>Statistiques</h2>
                    
                    <p><?php echo $manga['mangajap_manga_score']; ?></p>
                    <p><?php echo $manga['mangajap_manga_ranked']; ?></p>
                    <p><?php echo $manga['mangajap_manga_popularity']; ?></p>
                    <p><?php echo $manga['mangajap_manga_members']; ?></p>
                    <p><?php echo $manga['mangajap_manga_favorites']; ?></p>
                    
                </article>
                
                <article>
                    <h2>Synopsis</h2>
                    
                    <p><?php echo $manga['mangajap_manga_synopsis']; ?></p>
                    
                </article>
                
            </section>
            
        </div>

        <?php include ($_SERVER['DOCUMENT_ROOT'].'/common/footer.php'); ?>

    </body>
</html>

