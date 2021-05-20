<?php

$genres = json_decode(file_get_contents('https://mangajap.000webhostapp.com/api/genres?page[limit]=1000&sort=title_fr'), true);
$themes = json_decode(file_get_contents('https://mangajap.000webhostapp.com/api/themes?page[limit]=1000&sort=title_fr'), true);
$peoples = json_decode(file_get_contents('https://mangajap.000webhostapp.com/api/people?page[limit]=1000&sort=firstName'), true);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un manga | MangaJap</title>
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
        const mangaOrigin = {
            'jp': 'Japon',
            'kr': 'Corée du sud',
        };
        const mangaStatus = {
            'publishing': 'En cours',
            'finished': 'Terminé',
        };
        const mangaType = {
            'bd': 'BD',
            'comics': 'Comics',
            'josei': 'Josei',
            'kodomo': 'Kodomo',
            'seijin': 'Seijin',
            'seinen': 'Seinen',
            'shojo': 'Shōjo',
            'shonen': 'Shōnen',
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

        const manga = new Manga();
    </script>
</head>
<body>

<form method="post" action="" enctype = "multipart/form-data" onsubmit="return isValid();">
    <table>
        <tbody>

        <tr>
            <th>Titre</th>
            <td>
                <input type="text" onkeyup="manga.canonicalTitle = this.value">
            </td>
        </tr>

        <tr>
            <th>Titre français</th>
            <td>
                <input type="text" onkeyup="manga.title_fr = this.value">
            </td>
        </tr>

        <tr>
            <th>Titre anglais</th>
            <td>
                <input type="text" onkeyup="manga.title_en = this.value">
            </td>
        </tr>

        <tr>
            <th>Titre romanisé</th>
            <td>
                <input type="text" onkeyup="manga.title_en_jp = this.value">
            </td>
        </tr>

        <tr>
            <th>Titre japonais</th>
            <td>
                <input type="text" onkeyup="manga.title_ja_jp = this.value">
            </td>
        </tr>

        <tr>
            <th>Date de sortie</th>
            <td>
                <input type="date" onkeyup="manga.startDate = this.value">
            </td>
        </tr>

        <tr>
            <th>Date de fin</th>
            <td>
                <input type="date" onkeyup="manga.endDate = this.value">
            </td>
        </tr>

        <tr>
            <th>Pays</th>
            <td>
                <select onchange="manga.origin = this.value">
                    <option value="">-------</option>
                    <option value="jp">Japon</option>
                    <option value="fr">France</option>
                </select>
            </td>
        </tr>

        <tr>
            <th>Status de publication</th>
            <td>
                <select onchange="manga.status = this.value">
                    <option value="">-------</option>
                    <option value="publishing">En cours</option>
                    <option value="finished">Terminé</option>
                </select>
            </td>
        </tr>

        <tr>
            <th>Type</th>
            <td>
                <select onchange="manga.mangaType = this.value">
                    <option value="">-------</option>
                    <option value="bd">BD</option>
                    <option value="comics">Comics</option>
                    <option value="josei">Josei</option>
                    <option value="kodomo">Kodomo</option>
                    <option value="seijin">Seijin</option>
                    <option value="seinen">Seinen</option>
                    <option value="shojo">Shōjo</option>
                    <option value="shonen">Shōnen</option>
                </select>
            </td>
        </tr>

        <tr>
            <th>Nombre de volumes</th>
            <td>
                <input type="number" onkeyup="manga.volumeCount = this.value" onchange="addVolumes(this.value)">
                <div id="volume-container" style="max-height: 600px; overflow-y: auto"></div>
            </td>
        </tr>

        <tr>
            <th>Nombre de chapitres</th>
            <td>
                <input type="number" onkeyup="manga.chapterCount = this.value">
            </td>
        </tr>

        <tr>
            <th>Synopsis</th>
            <td>
                <textarea rows="8" cols="33" onkeyup="manga.synopsis = this.value"></textarea>
            </td>
        </tr>

        <tr>
            <th>Image</th>
            <td>
                <input type="file" accept="image/*" onchange="getBase64(this.files[0], function(base64) { manga.coverImage = base64 })">
            </td>
        </tr>

        <tr>
            <th>Bannière</th>
            <td>
                <input type="file" accept="image/*" onchange="getBase64(this.files[0], function(base64) { manga.bannerImage = base64 })">
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
                <input type="submit" name="submit" value="Envoyer">
            </td>
        </tr>

        </tbody>
    </table>
</form>

<script>
    function isValid() {
        console.log(manga); // TODO: on peut supposer que les non administrateurs m'enverrait cette variable et que je la validerais pour ajouter un manga
        manga.create();
        return false;
    }

    function addVolumes(volumeCount) {
        for (let volumeNumber=1; volumeNumber<=parseInt(volumeCount); volumeNumber++) {
            const volume = new Volume();
            manga.volumes.push(volume);

            volume.manga = manga;

            const container = document.getElementById('volume-container');

            const element = document.createElement('div');
            element.id = `volume-${generateId()}`;

            volume.number = volumeNumber;

            const spanElement = document.createElement('span');
            spanElement.innerHTML = `${volume.number}`;
            element.appendChild(spanElement);

            const titleFrInput = document.createElement('input');
            titleFrInput.type = 'text';
            titleFrInput.placeholder = 'Titre fr';
            titleFrInput.onkeyup = function() { volume.title_fr = this.value }
            element.appendChild(titleFrInput);

            const titleEnInput = document.createElement('input');
            titleEnInput.type = 'text';
            titleEnInput.placeholder = 'Titre en';
            titleEnInput.onkeyup = function() { volume.title_en = this.value }
            element.appendChild(titleEnInput);

            const titleEnJpInput = document.createElement('input');
            titleEnJpInput.type = 'text';
            titleEnJpInput.placeholder = 'Titre en_jp';
            titleEnJpInput.onkeyup = function() { volume.title_en_jp = this.value }
            element.appendChild(titleEnJpInput);

            const titleJaJpInput = document.createElement('input');
            titleJaJpInput.type = 'text';
            titleJaJpInput.placeholder = 'Titre ja_jp';
            titleJaJpInput.onkeyup = function() { volume.title_ja_jp = this.value }
            element.appendChild(titleJaJpInput);

            const startChapterInput = document.createElement('input');
            startChapterInput.type = 'number';
            startChapterInput.placeholder = 'Start chapter';
            startChapterInput.onkeyup = function() { volume.startChapter = this.value }
            element.appendChild(startChapterInput);

            const endChapterInput = document.createElement('input');
            endChapterInput.type = 'number';
            endChapterInput.placeholder = 'End chapter';
            endChapterInput.onkeyup = function() { volume.endChapter = this.value }
            element.appendChild(endChapterInput);

            const airDateInput = document.createElement('input');
            airDateInput.type = 'date';
            airDateInput.placeholder = 'Date de diffusion';
            airDateInput.onchange = function() { volume.published = this.value }
            element.appendChild(airDateInput);

            container.appendChild(element);
        }
    }

    function addGenreField() {
        const genre = new Genre();
        manga.genres.push(genre);

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
        manga.genres.push(genre);

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
        manga.genres.splice(manga.genres.indexOf(genre), 1);
    }

    function addThemeField() {
        const theme = new Theme();
        manga.themes.push(theme);

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
        manga.themes.push(theme);

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
        manga.themes.splice(manga.themes.indexOf(theme), 1);
    }

    function addStaffField() {
        const staff = new Staff();
        manga.staff.push(staff);
        
        staff.manga = manga;

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
        for (const key in staffRole) {
            const option = document.createElement("option");
            option.value = key;
            option.text = staffRole[key];
            roleSelect.appendChild(option);
        }
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
        manga.staff.push(staff);
        
        staff.manga = manga;

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
        for (const key in staffRole) {
            const option = document.createElement("option");
            option.value = key;
            option.text = staffRole[key];
            roleSelect.appendChild(option);
        }
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
        manga.staff.splice(manga.staff.indexOf(staff), 1);
    }

    function addFranchiseField() {
        const franchise = new Franchise();
        manga.franchise.push(franchise);
        
        franchise.source = manga;

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
            xhr.open('GET', `/api/manga?filter[query]=${this.value}`, true);
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
        for (const key in franchiseRole) {
            const option = document.createElement("option");
            option.value = key;
            option.text = franchiseRole[key];
            roleSelect.appendChild(option);
        }
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
        manga.franchise.splice(manga.franchise.indexOf(franchise), 1);
    }
</script>

</body>
</html>