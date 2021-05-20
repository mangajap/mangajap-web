<?php

$genres = json_decode(file_get_contents('https://mangajap.000webhostapp.com/api/genres?page[limit]=1000&sort=title_fr'), true);
$themes = json_decode(file_get_contents('https://mangajap.000webhostapp.com/api/themes?page[limit]=1000&sort=title_fr'), true);
$peoples = json_decode(file_get_contents('https://mangajap.000webhostapp.com/api/people?page[limit]=1000&sort=firstName'), true);
$animeOrigin = [
    'jp' => 'Japon',
    'kr' => 'Corée du sud',
];
$animeStatus = [
    'airing' => 'En cours',
    'finished' => 'Terminé',
    'planned' => 'Pas encore commencé',
];
$animeType = [
    'tv' => 'Série TV',
    'movie' => 'Film',
    'oav' => 'OAV',
];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un anime | MangaJap</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="scripts/models.js"></script>
    <script>
        function onBeforeUnload(e) {
            e.preventDefault();
            e.returnValue = '';
        }

        window.addEventListener('beforeunload', onBeforeUnload);
    </script>
    <script>
        function getBase64(file, callback) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
                callback(reader.result.split(',')[1]);
            };
            reader.onerror = function (error) {
                throw `Exception: Unable to convert image to base 64\n${error}`;
            };
        }

        function generateId() {
            return Math.random().toString(36).substr(2, 9).toString();
        }
    </script>
    <script>
        const genres = <?php echo json_encode($genres); ?>;
        const themes = <?php echo json_encode($themes); ?>;
        const peoples = <?php echo json_encode($peoples); ?>;
        const animeOrigin = {
            'jp': 'Japon',
            'kr': 'Corée du sud',
        };
        const animeStatus = {
            'airing': 'En cours',
            'finished': 'Terminé',
            'planned': 'Pas encore commencé',
        };
        const animeType = {
            'tv': 'Série TV',
            'movie': 'Film',
            'oav': 'OAV',
        };
        const staffRole = {
            'story_and_art': 'Créateur',
            'story': 'Auteur',
            'illustrator': 'Dessinateur',
            'original_creator': 'Créateur original',
        };
        const franchiseRole = {
            'adaptation': 'adaptation',
            'alternative_setting': 'alternative_setting',
            'alternative_version': 'alternative_version',
            'character': 'character',
            'full_story': 'full_story',
            'other': 'other',
            'parent_story': 'parent_story',
            'prequel': 'prequel',
            'sequel': 'sequel',
            'side_story': 'side_story',
            'spinoff': 'spinoff',
            'summary': 'summary',
        };

        const anime = new Anime();
    </script>
</head>
<body>

    <form id="form" method="post" action="" enctype = "multipart/form-data" onsubmit="return isValid();">
        <table>
            <tbody>

            <tr>
                <th>
                    <label for="canonicalTitle">Titre</label>
                </th>
                <td>
                    <input id="canonicalTitle" type="text" onkeyup="anime.canonicalTitle = this.value">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="title_fr">Titre français</label>
                </th>
                <td>
                    <input id="title_fr" type="text" onkeyup="anime.title_fr = this.value">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="title_en">Titre anglais</label>
                </th>
                <td>
                    <input id="title_en" type="text" onkeyup="anime.title_en = this.value">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="title_en_jp">Titre romanisé</label>
                </th>
                <td>
                    <input id="title_en_jp" type="text" onkeyup="anime.title_en_jp = this.value">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="title_ja_jp">Titre japonais</label>
                </th>
                <td>
                    <input id="title_ja_jp" type="text" onkeyup="anime.title_ja_jp = this.value">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="startDate">Date de sortie</label>
                </th>
                <td>
                    <input id="startDate" type="date" onchange="anime.startDate = this.value">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="endDate">Date de fin</label>
                </th>
                <td>
                    <input id="endDate" type="date" onchange="anime.endDate = this.value">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="origin">Pays</label>
                </th>
                <td>
                    <select id="origin" onchange="anime.origin = this.value">
                        <option value="">-------</option>
                        <?php foreach ($animeOrigin as $value => $country) : ?>
                            <option value=<?php print $value; ?>>
                                <?php echo $country; ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="status">Status de publication</label>
                </th>
                <td>
                    <select id="status" onchange="anime.status = this.value">
                        <option value="">-------</option>
                        <?php foreach ($animeStatus as $value => $status) : ?>
                            <option value=<?php print $value; ?>>
                                <?php echo $status; ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="animeType">Type</label>
                </th>
                <td>
                    <select id="animeType" onchange="anime.animeType = this.value">
                        <option value="">-------</option>
                        <?php foreach ($animeType as $value => $type) : ?>
                            <option value=<?php print $value; ?>>
                                <?php echo $type; ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th>Episodes</th>
                <td>
                    <div>
                        <input id="seasonCount" type="number" placeholder="Nombre de saisons"">
                    </div>
                    <div id="season-container" style="max-height: 600px; overflow-y: auto"></div>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="episodeLength">Durée par épisode</label>
                </th>
                <td>
                    <input id="episodeLength" type="number" onkeyup="anime.episodeLength = this.value">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="synopsis">Synopsis</label>
                </th>
                <td>
                    <textarea id="synopsis" rows="8" cols="33" onkeyup="anime.synopsis = this.value"></textarea>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="coverImage">Image</label>
                </th>
                <td>
                    <input id="coverImage" type="file" accept="image/*" onchange="getBase64(this.files[0], function(base64) { anime.coverImage = base64 })">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="youtubeVideoId">Vidéo youtube</label>
                </th>
                <td>
                    <input id="youtubeVideoId" type="text" onkeyup="anime.youtubeVideoId = this.value">
                </td>
            </tr>

            <tr>
                <th>Genres</th>
                <td>
                    <div id="genre-container"></div>
                    <p>
                        <a href="javascript: void(0)" onclick="addGenreField()">+ Ajouter un genre</a>
                        <br/>
                        Si le genre n'est pas sur le site, <a href="javascript: void(0)" onclick="addNewGenreField()">merci de le créer</a>.
                    </p>
                </td>
            </tr>

            <tr>
                <th>Themes</th>
                <td>
                    <div id="theme-container"></div>
                    <p>
                        <a href="javascript: void(0)" onclick="addThemeField()">+ Ajouter un thème</a>
                        <br/>
                        Si le thème n'est pas sur le site, <a href="javascript: void(0)" onclick="addNewThemeField()">merci de le créer</a>.
                    </p>
                </td>
            </tr>

            <tr>
                <th>Staff</th>
                <td>
                    <div id="staff-container"></div>
                    <p>
                        <a href="javascript: void(0)" onclick="addStaffField()">+ Ajouter un staff</a>
                        <br/>
                        Si la personne n'est pas sur le site, <a href="javascript: void(0)" onclick="addNewStaffField()">merci de le créer</a>.
                    </p>
                </td>
            </tr>

            <tr>
                <th>Franchise</th>
                <td>
                    <div id="franchise-container"></div>
                    <p>
                        <a href="javascript: void(0)" onclick="addFranchiseField()">+ Ajouter une franchise</a>
                    </p>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <input type="submit" value="Envoyer">
                </td>
            </tr>

            </tbody>
        </table>
    </form>

    <script>
        function isValid() {
            console.log(anime); // TODO: on peut supposer que les non administrateurs m'enverrait cette variable et que je la validerais pour ajouter un anime
            anime.create();
            return false;
        }

        document.getElementById("seasonCount").onchange = function() {
            for (let seasonNumber=1; seasonNumber<=parseInt(document.getElementById("seasonCount").value); seasonNumber++) {
                addSeasonField(seasonNumber);
            }
        };

        function addSeasonField(seasonNumber) {
            const container = document.getElementById("season-container");

            const element = document.createElement('div');
            const elementId = "season" + seasonNumber;
            element.setAttribute('id', elementId);

            let html = `
                <div>
                    <span>Saison</span>
                    <span>${seasonNumber}</span>
                    <input id='${elementId + "-episodeCount"}' type='number' placeholder='Nombres d&apos;épisodes'>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Titre français</th>
                            <th>Titre anglais</th>
                            <th>Titre romanisé</th>
                            <th>Titre japonais</th>
                            <th>Date de diffusion</th>
                        </tr>
                    </thead>
                <tbody id='${elementId + "-container"}'></tbody>
            `;

            element.innerHTML = html;
            container.appendChild(element);


            document.getElementById(elementId+"-episodeCount").addEventListener("change", function() {
                for (let relativeNumber=1; relativeNumber<=parseInt(document.getElementById(elementId+"-episodeCount").value); relativeNumber++) {
                    addEpisodeField(elementId, seasonNumber, relativeNumber);
                }
            });

            return elementId;
        }
        function addEpisodeField(seasonId, seasonNumber, relativeNumber) {
            const episode = new Episode();
            anime.episodes.push(episode);
            
            episode.anime = anime;

            const container = document.getElementById(seasonId + "-container");

            const element = document.createElement('tr');

            episode.number = anime.episodes.length;
            episode.seasonNumber = seasonNumber;
            episode.relativeNumber = relativeNumber;

            let tdElement = document.createElement('td');
            const spanElement = document.createElement('span');
            spanElement.innerHTML = `S${episode.seasonNumber}E${episode.relativeNumber} (${episode.number})`;
            tdElement.appendChild(spanElement);
            element.appendChild(tdElement);

            tdElement = document.createElement('td');
            const titleFrInput = document.createElement('input');
            titleFrInput.type = 'text';
            titleFrInput.placeholder = 'Titre fr';
            titleFrInput.onkeyup = function() { episode.title_fr = this.value }
            tdElement.appendChild(titleFrInput);
            element.appendChild(tdElement);

            tdElement = document.createElement('td');
            const titleEnInput = document.createElement('input');
            titleEnInput.type = 'text';
            titleEnInput.placeholder = 'Titre en';
            titleEnInput.onkeyup = function() { episode.title_en = this.value }
            tdElement.appendChild(titleEnInput);
            element.appendChild(tdElement);

            tdElement = document.createElement('td');
            const titleEnJpInput = document.createElement('input');
            titleEnJpInput.type = 'text';
            titleEnJpInput.placeholder = 'Titre en_jp';
            titleEnJpInput.onkeyup = function() { episode.title_en_jp = this.value }
            tdElement.appendChild(titleEnJpInput);
            element.appendChild(tdElement);

            tdElement = document.createElement('td');
            const titleJaJpInput = document.createElement('input');
            titleJaJpInput.type = 'text';
            titleJaJpInput.placeholder = 'Titre ja_jp';
            titleJaJpInput.onkeyup = function() { episode.title_ja_jp = this.value }
            tdElement.appendChild(titleJaJpInput);
            element.appendChild(tdElement);

            tdElement = document.createElement('td');
            const airDateInput = document.createElement('input');
            airDateInput.type = 'date';
            airDateInput.placeholder = 'Date de diffusion';
            airDateInput.onchange = function() { episode.airDate = this.value }
            tdElement.appendChild(airDateInput);
            element.appendChild(tdElement);

            container.appendChild(element);
        }

        function addGenreField() {
            const genre = new Genre();
            anime.genres.push(genre);

            const container = document.getElementById('genre-container');

            const element = document.createElement('div');
            element.id = `genre-${generateId()}`;

            const selectElement = document.createElement("select");
            selectElement.onchange = function() { genre.id = this.value };

            for (let i = 0; i<genres.data.length; i++) {
                if (i === 0) {
                    const option = document.createElement("option");
                    option.value = '';
                    option.text = '-------';
                    selectElement.appendChild(option);
                }
                const option = document.createElement("option");
                option.value = genres.data[i].id;
                option.text = genres.data[i].attributes.title;
                selectElement.appendChild(option);
            }
            element.appendChild(selectElement);

            const a = document.createElement('a');
            a.href =  'javascript: void(0)';
            a.onclick = function() { removeGenreField(element.id, genre) }
            a.innerHTML = '<span>X</span>';
            element.appendChild(a);

            container.appendChild(element);
        }
        function addNewGenreField() {
            const genre = new Genre();
            anime.genres.push(genre);

            const container = document.getElementById('genre-container');

            const element = document.createElement('div');
            element.id = `genre-${generateId()}`;

            const titleInput = document.createElement('input');
            titleInput.type = 'text';
            titleInput.onkeyup = function() { genre.title_fr = this.value }
            titleInput.placeholder = 'Nom français';
            element.appendChild(titleInput);

            const descriptionInput = document.createElement('input');
            descriptionInput.type = 'text';
            descriptionInput.onkeyup = function() { genre.description = this.value }
            descriptionInput.placeholder = 'Description';
            element.appendChild(descriptionInput);

            const a = document.createElement('a');
            a.href =  'javascript: void(0)';
            a.onclick = function() { removeGenreField(element.id, genre) }
            a.innerHTML = '<span>X</span>';
            element.appendChild(a);

            container.appendChild(element);
        }
        function removeGenreField(id, genre) {
            const container = document.getElementById('genre-container');
            const element = document.getElementById(id);

            container.removeChild(element);
            anime.genres.splice(anime.genres.indexOf(genre), 1);
        }

        function addThemeField() {
            const theme = new Theme();
            anime.themes.push(theme);

            const container = document.getElementById('theme-container');

            const element = document.createElement('div');
            element.id = `theme-${generateId()}`;

            const selectElement = document.createElement("select");
            selectElement.onchange = function() { theme.id = this.value };

            for (let i = 0; i<themes.data.length; i++) {
                if (i === 0) {
                    const option = document.createElement("option");
                    option.value = '';
                    option.text = '-------';
                    selectElement.appendChild(option);
                }
                const option = document.createElement("option");
                option.value = themes.data[i].id;
                option.text = themes.data[i].attributes.title;
                selectElement.appendChild(option);
            }
            element.appendChild(selectElement);

            const a = document.createElement('a');
            a.href =  'javascript: void(0)';
            a.onclick = function() { removeThemeField(element.id, theme) }
            a.innerHTML = '<span>X</span>';
            element.appendChild(a);

            container.appendChild(element);
        }
        function addNewThemeField() {
            const theme = new Theme();
            anime.themes.push(theme);

            const container = document.getElementById('theme-container');

            const element = document.createElement('div');
            element.id = `theme-${generateId()}`;

            const titleInput = document.createElement('input');
            titleInput.type = 'text';
            titleInput.onkeyup = function() { theme.title_fr = this.value }
            titleInput.placeholder = 'Nom français';
            element.appendChild(titleInput);

            const descriptionInput = document.createElement('input');
            descriptionInput.type = 'text';
            descriptionInput.onkeyup = function() { theme.description = this.value }
            descriptionInput.placeholder = 'Description';
            element.appendChild(descriptionInput);

            const a = document.createElement('a');
            a.href =  'javascript: void(0)';
            a.onclick = function() { removeThemeField(element.id, theme) }
            a.innerHTML = '<span>X</span>';
            element.appendChild(a);

            container.appendChild(element);
        }
        function removeThemeField(id, theme) {
            const container = document.getElementById('theme-container');
            const element = document.getElementById(id);

            container.removeChild(element);
            anime.themes.splice(anime.themes.indexOf(theme), 1);
        }

        function addStaffField() {
            const staff = new Staff();
            anime.staff.push(staff);
            
            staff.anime = anime;

            const container = document.getElementById('staff-container');

            const element = document.createElement('div');
            element.id = `staff-${generateId()}`;

            const peopleSelect = document.createElement("select");
            peopleSelect.onchange = function() { staff.people.id = this.value; };
            for (let i=0; i<peoples.data.length; i++) {
                if (i === 0) {
                    const option = document.createElement("option");
                    option.value = '';
                    option.text = '-------';
                    peopleSelect.appendChild(option);
                }
                const option = document.createElement("option");
                option.value = peoples.data[i].id;
                option.text = peoples.data[i].attributes.firstName + " " + peoples.data[i].attributes.lastName + " / " + peoples.data[i].attributes.pseudo;
                peopleSelect.appendChild(option);
            }
            element.appendChild(peopleSelect);

            const roleSelect = document.createElement("select");
            roleSelect.onchange = function() { staff.role = this.value; };
            const option = document.createElement("option");
            option.value = '';
            option.text = '-------';
            roleSelect.appendChild(option);
            Object.entries(staffRole).forEach(function([value, name]) {
                const option = document.createElement("option");
                option.value = value;
                option.text = name;
                roleSelect.appendChild(option);
            });
            element.appendChild(roleSelect);

            const a = document.createElement('a');
            a.href =  'javascript: void(0)';
            a.onclick = function() { removeStaffField(element.id, staff) }
            a.innerHTML = '<span>X</span>';
            element.appendChild(a);

            container.appendChild(element);
        }
        function addNewStaffField() {
            const staff = new Staff();
            anime.staff.push(staff);
            
            staff.anime = anime;

            const container = document.getElementById('staff-container');

            const element = document.createElement('div');
            element.id = `staff-${generateId()}`;

            const peopleFirstNameInput = document.createElement('input');
            peopleFirstNameInput.type = 'text';
            peopleFirstNameInput.onkeyup = function() { staff.people.firstName = this.value }
            peopleFirstNameInput.placeholder = 'Prénom';
            element.appendChild(peopleFirstNameInput);

            const peopleLastNameInput = document.createElement('input');
            peopleLastNameInput.type = 'text';
            peopleLastNameInput.onkeyup = function() { staff.people.lastName = this.value }
            peopleLastNameInput.placeholder = 'Nom';
            element.appendChild(peopleLastNameInput);

            const peoplePseudoInput = document.createElement('input');
            peoplePseudoInput.type = 'text';
            peoplePseudoInput.onkeyup = function() { staff.people.pseudo = this.value }
            peoplePseudoInput.placeholder = 'Pseudo';
            element.appendChild(peoplePseudoInput);

            const roleSelect = document.createElement("select");
            roleSelect.onchange = function() { staff.role = this.value; };
            const option = document.createElement("option");
            option.value = '';
            option.text = '-------';
            roleSelect.appendChild(option);
            Object.entries(staffRole).forEach(function([value, name]) {
                const option = document.createElement("option");
                option.value = value;
                option.text = name;
                roleSelect.appendChild(option);
            });
            element.appendChild(roleSelect);


            const a = document.createElement('a');
            a.href =  'javascript: void(0)';
            a.onclick = function() { removeStaffField(element.id, staff) }
            a.innerHTML = '<span>X</span>';
            element.appendChild(a);

            container.appendChild(element);
        }
        function removeStaffField(id, staff) {
            const container = document.getElementById('staff-container');
            const element = document.getElementById(id);

            container.removeChild(element);
            anime.staff.splice(anime.staff.indexOf(staff), 1);
        }

        function addFranchiseField() {
            const franchise = new Franchise();
            anime.franchise.push(franchise);
            
            franchise.source = anime;
            
            const container = document.getElementById('franchise-container');

            const element = document.createElement('div');
            element.id = `franchise-${generateId()}`;

            const listSelect = document.createElement("select");
            listSelect.id = `franchise-media-list-${generateId()}`;
            listSelect.onchange = function () {
                const identifier = JSON.parse(this.value);
                switch (identifier.type) {
                    case 'manga':
                        franchise.destination = new Manga();
                        franchise.destination.id = identifier.id;
                        break;
                    case 'anime':
                        franchise.destination = new Anime();
                        franchise.destination.id = identifier.id;
                        break;
                }
            }
            listSelect.innerHTML = "<option value>-------</option>";

            let searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'Rechercher';
            searchInput.onchange = function() {
                listSelect.innerHTML = "<option value=''>-------</option>";

                let xhr = new XMLHttpRequest();
                xhr.onload = function () {
                    try {
                        const response = JSON.parse(this.responseText);
                        for (let i = 0; i<response.data.length; i++) {
                            const option = document.createElement("option");
                            option.value = JSON.stringify({
                                'type': response.data[i].type,
                                'id': response.data[i].id
                            });
                            option.text = response.data[i].attributes.canonicalTitle + " / " + response.data[i].type;
                            listSelect.appendChild(option);
                        }
                    } catch (e) {
                        throw 'JSON is not valid: ' + this.responseText;
                    }
                };
                xhr.open('GET', `/api/manga?filter[query]=${this.value}`, true);
                xhr.send(null);

                xhr = new XMLHttpRequest();
                xhr.onload = function () {
                    try {
                        const response = JSON.parse(this.responseText);
                        for (let i = 0; i<response.data.length; i++) {
                            const option = document.createElement("option");
                            option.value = JSON.stringify({
                                'type': response.data[i].type,
                                'id': response.data[i].id
                            });
                            option.text = response.data[i].attributes.canonicalTitle + " / " + response.data[i].type;
                            listSelect.appendChild(option);
                        }
                    } catch (e) {
                        throw 'JSON is not valid: ' + this.responseText;
                    }
                };
                xhr.open('GET', `/api/anime?filter[query]=${this.value}`, true);
                xhr.send(null);
            }
            element.appendChild(searchInput);

            element.appendChild(listSelect);

            const roleSelect = document.createElement("select");
            roleSelect.onchange = function() { franchise.role = this.value; };
            const option = document.createElement("option");
            option.value = '';
            option.text = '-------';
            roleSelect.appendChild(option);
            Object.entries(franchiseRole).forEach(function([value, name]) {
                const option = document.createElement("option");
                option.value = value;
                option.text = name;
                roleSelect.appendChild(option);
            });
            element.appendChild(roleSelect);

            const a = document.createElement('a');
            a.href =  'javascript: void(0)';
            a.onclick = function() { removeFranchiseField(element.id, franchise) }
            a.innerHTML = '<span>X</span>';
            element.appendChild(a);
            
            container.appendChild(element);
        }
        function removeFranchiseField(id, franchise) {
            const container = document.getElementById('franchise-container');
            const element = document.getElementById(id);

            container.removeChild(element);
            anime.franchise.splice(anime.franchise.indexOf(franchise), 1);
        }
    </script>

</body>
</html>