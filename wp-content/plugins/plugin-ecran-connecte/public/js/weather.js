var meteoRequest = new XMLHttpRequest();
var startUrl = "https://api.openweathermap.org/data/2.5/weather?lat=";
var url;
var endUrl = "&lang=fr&APPID=ae546c64c1c36e47123b3d512efa723e";

function success(pos){
    var crd = pos.coords;

    var longitude = crd.longitude;
    var latitude = crd.latitude;

    url = startUrl + latitude + "&lon=" + longitude + endUrl;

    meteoRequest.open('GET', url, true);
    meteoRequest.setRequestHeader('Accept', 'application/json');
    meteoRequest.send();


}

function error(err) {
    console.warn(`ERROR(${err.code}): ${err.message}`);
}


/**
 * Display the weather
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

        // Bloc météo
        var weather = document.createElement("DIV");
        weather.id = "weather";
        weather.className = "weather-box"; // Ajout d'une classe pour styliser

        var imgTemp = document.createElement("IMG");
        imgTemp.id = "icon";
        imgTemp.src = "/wp-content/plugins/plugin-ecran-connecte/public/img/" + getIcon(json) + ".png";
        imgTemp.alt = getAlt(json);

        var tempText = document.createElement("DIV");
        tempText.className = "weather-text";
        tempText.innerHTML = temp + "<span class=\"degree\">°C</span>";

        weather.appendChild(imgTemp); // Icone en premier
        weather.appendChild(tempText); // Texte en dessous
        div.appendChild(weather); // Ajout au conteneur principal

        // Bloc vent
        var wind = document.createElement("DIV");
        wind.id = "wind";
        wind.className = "wind-box"; // Ajout d'une classe pour styliser

        var imgVent = document.createElement("IMG");
        imgVent.src = "/wp-content/plugins/plugin-ecran-connecte/public/img/wind.png";
        imgVent.alt = "Icône du vent";

        var windText = document.createElement("DIV");
        windText.className = "wind-text";
        windText.innerHTML = vent + "<span class=\"kmh\">km/h</span>";

        wind.appendChild(imgVent); // Icone en premier
        wind.appendChild(windText); // Texte en dessous
        div.appendChild(wind); // Ajout au conteneur principal
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
    return str.slice(0, -1);
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