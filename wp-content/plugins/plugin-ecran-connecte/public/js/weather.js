var meteoRequest = new XMLHttpRequest();
// var longitude = 5.4510;
// var latitude = 43.5156;
var startUrl = "https://api.openweathermap.org/data/2.5/weather?lat=";
var endUrl = "&lang=fr&APPID=ae546c64c1c36e47123b3d512efa723e";


/**
* @param La position d'un appareil.
*
* Utilise la position d'un appareil pour avoir la lattitude et la longitude de ce dernier et les insérer dans un lien.
* Ce lien est ensuite utilisé dans XMLHttpRequest meteoRequest afin de l'adapter à la page WordPress.
* */
function success(pos){
    // On récupère les coordonnées de la postition.
    const crd = pos.coords;

    // On déclare nos variables latitude et longitude.
    var latitude = crd.latitude;
    var longitude = crd.longitude;

    // On crée l'URL permettant de connaitre la météo à la latitude et longitude demandée.
    var url = startUrl + latitude + "&lon=" + longitude + endUrl;

    // On fait appel à meteoRequest.
    meteoRequest.open('GET', url, true);
    meteoRequest.setRequestHeader('Accept', 'application/json');
    meteoRequest.send();
}

/**
* Permet d'afficher une erreur si la position de l'appareil n'est pas trouvé.
* */
function error(err) {
    console.warn(`ERROR(${err.code}): ${err.message}`);
}

/**
 * Permet de rafraichir la météo. Si le navigateur supporte l'API, il localise l'appareil utilisé et lance soit
 * la fonction success(), soit la fonction error().
 */
function refreshWeather() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error);
    }
}

meteoRequest.onload = function () {
    var json = JSON.parse(this.responseText);
    var temp = Math.round(getTemp(json));
    var vent = getWind(json).toFixed(0);
    if (document.getElementById('Weather') !== null) {
        var div = document.getElementById('Weather');
        div.innerHTML = "";
        var weather = document.createElement("DIV");
        weather.innerHTML = temp + "<span class=\"degree\">°C</span>";
        weather.id = "weather";
        var imgTemp = document.createElement("IMG");
        imgTemp.id = "icon";
        imgTemp.src = "/wp-content/plugins/plugin-ecran-connecte/public/img/" + getIcon(json) + ".png";
        imgTemp.alt = getAlt(json);
        weather.appendChild(imgTemp);
        var wind = document.createElement("DIV");
        wind.innerHTML = vent + "<span class=\"kmh\">km/h</span>";
        wind.id = "wind";
        var imgVent = document.createElement("IMG");
        imgVent.src = "/wp-content/plugins/plugin-ecran-connecte/public/img/wind.png";
        imgVent.alt = "Img du vent";
        wind.appendChild(imgVent);
        div.appendChild(weather);
        div.appendChild(wind);
        setTimeout(refreshWeather, 900000);
    }
};

/** Getter **/
function getAlt(json) {
    return json["weather"][0]["description"];
}

function getIcon(json) {
    return cutIcon(json["weather"][0]["icon"]);
}

function cutIcon(str) {
    return str.substr(0, str.length - 1);
}

function getTemp(json) {
    return kelvinToC(json["main"]["temp"]);
}

function kelvinToC(kelvin) {
    return kelvin - 273.15;
}

function getWind(json) {
    return msToKmh(json["wind"]["speed"]);
}

function msToKmh(speed) {
    return speed * 3.6;
}

refreshWeather();