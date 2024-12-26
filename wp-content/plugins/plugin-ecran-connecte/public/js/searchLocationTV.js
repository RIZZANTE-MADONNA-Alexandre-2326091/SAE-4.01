/**
 * Handles the successful retrieval of the user's position coordinates.
 *
 * @param {GeolocationPosition} pos The geolocation position object containing the user's current location.
 * @return {void} This function does not return a value.
 */
function success(pos){
    var crd = pos.coords;

    searchLocation(crd.longitude, crd.latitude);
}

/**
 * Logs an error message to the console with the provided error details.
 *
 * @param {Object} err - The error object containing details of the error.
 * @param {number} err.code - The error code representing the type of error.
 * @param {string} err.message - The message describing the details of the error.
 * @return {void} This function does not return a value.
 */
function error(err) {
    console.warn(`ERROR(${err.code}): ${err.message}`);
}

/**
 * Sends the longitude and latitude to the server for processing the location.
 *
 * @param {number} longitude - The longitude of the location to search.
 * @param {number} latitude - The latitude of the location to search.
 * @return {void} This function does not return a value but processes and logs the server's response or any errors encountered.
 */
function searchLocation(longitude, latitude){
    const formData = new FormData();

    formData.append("action", "process_location");
    formData.append("longitude", longitude);
    formData.append("latitude", latitude);

    fetch("your-server-endpoint", {
        method: "POST",
        body: formData,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            console.log("Succès:", data);
        })
        .catch((error) => {
            console.error("Erreur:", error);
        });
}

navigator.geolocation.getCurrentPosition(success, error);