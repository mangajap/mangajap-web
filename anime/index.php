<?php

$json = json_decode(file_get_contents("https://mangajap.000webhostapp.com/api/anime?page[limit]=20"),
    true);

$data = $json['data'];

?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>Anime | MangaJap</title>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width"/>
        
        <style><?php include($_SERVER['DOCUMENT_ROOT'].'/css/style.css'); ?></style>
        
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    </head>
    
    <body>

        <?php include ($_SERVER['DOCUMENT_ROOT'].'/common/header.php'); ?>

        <div class="container">
            
            <div class="manga-grid">
                <?php foreach ($data as $anime) : ?>
                    <div class="manga-item">

                        <a class="manga-link"
                           href="javascript: void(0)"></a>

                        <img class="manga-cover"
                             src=<?php echo $anime['attributes']['coverImage']; ?> />

                        <div class="manga-title">
                            <span><?php echo $anime['attributes']['canonicalTitle']; ?></span>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
            
        </div>

        <?php include ($_SERVER['DOCUMENT_ROOT'].'/common/footer.php'); ?>

    </body>
</html>