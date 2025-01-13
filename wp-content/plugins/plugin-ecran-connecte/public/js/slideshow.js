let urlUpload = "/wp-content/uploads/media/";
let pdfUrl = null;
let numPage = 0; // Numéro de page courante
let totalPage = null; // Nombre de pages
let endPage = false;
let stop = false;

/**
 * Ce bloc de 4 lignes sert à lancer de manière asynchrone l'IFrame Player API d'après la documentation officielle de [Youtube Iframe API](https://developers.google.com/youtube/iframe_api_reference?hl=fr)
 * */
let tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
let firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

/**
 * Lecteur de vidéo YouTube shorts
 * */
let playersh;

/**
 * Lecteur de vidéo YouTube classique
*/
let playerw;


let player;

/**
 * Temps en ms durant laquelle une information est affichée (par défaut 10s).
 * @defaultValue 10000
 */
let timeout = 10000;

/**
 * Diaporama des informations
 * */
let slidesShow;

/**
 * Diaporama de vidéos format "classique"
 * */
let slidesVideos;

/**
 * URL pour l'API YouTube Iframe
 * */
let urlYoutube;

infoSlideShow();
scheduleSlideshow();

/**
 * Fonction
 * @param slides Liste contenant les slides contenant les informations et auquel les vidéos de format classique sont supprimées
 * @return Liste contenant les vidéos de format classique
 * */
function separeVideosIntoASlide(slides) {
    let newSlides = [];
    //Récupérer les éléments
    const videoYT = document.querySelectorAll(".videow");
    const videoC = document.querySelectorAll(".localCvideo");
    const listeVideos = [];

    // Enlever les informations qui ne sont pas des vidéos classiques
    for (let i = 0; i < slides.length; ++i) {
        if (slides[i].childNodes) {
            for (let index = 0; index < slides[i].childNodes.length; ++index) {
                //Séparer les vidéos YouTube classiques
                for (let indexyt = 0; indexyt < videoYT.length; ++indexyt) {

                    // Si c'est une vidéo
                    if (videoYT[indexyt] === slides[i].childNodes[index]) {
                        if (listeVideos.indexOf(slides[i]) === -1) {
                            listeVideos.push(slides[i]);
                        }
                    }

                    //Sinon, on ajoute dans la nouvelle liste des autres informations
                    else {
                        if (newSlides.indexOf(slides[i]) === -1 && !(
                                slides[i].childNodes[1].className === 'localCvideo'
                                || slides[i].childNodes[1].className === 'videow')) {
                            newSlides.push(slides[i]);
                        }
                    }
                }
                //Séparer les vidéos locales classiques
                for (let indexc = 0; indexc < videoC.length; ++indexc) {

                    // Si c'est une vidéo
                    if (videoC[indexc] === slides[i].childNodes[index]) {
                        if (listeVideos.indexOf(slides[i]) === -1) {
                            listeVideos.push(slides[i]);
                        }
                    }

                    //Sinon, on ajoute dans la nouvelle liste des autres informations
                    else {
                        if (newSlides.indexOf(slides[i]) === -1 && !(
                                slides[i].childNodes[1].className === 'localCvideo'
                                || slides[i].childNodes[1].className === 'videow')) {
                            newSlides.push(slides[i]);
                        }
                    }
                }
            }
        }
    }
    slidesVideos = listeVideos;
    return newSlides;
}

/**
 * Begin a slideshow if there is some informations
 */
function infoSlideShow()
{
    if(document.getElementsByClassName("myInfoSlides").length > 0) {
        slidesShow = document.getElementsByClassName("myInfoSlides");
        slidesShow = separeVideosIntoASlide(slidesShow);
        console.log("-Début du diaporama");
        displayOrHide(slidesShow, 0);
    }
}

/**
 * Begin a slideshow if there is some informations
 */
function scheduleSlideshow()
{
    if(document.getElementsByClassName("mySlides").length > 0) {
        console.log("-Début du diaporama");
        displayOrHide(document.getElementsByClassName("mySlides"), 0);
    }
}


/**
 * Display a slideshow
 * @param slides Les slides (informations) qui défileront dans la partie "Information"
 * @param slideIndex index de la slide dans la liste {@linkcode slides}, c'est-à-dire le numéro de l'information
 */
function displayOrHide(slides, slideIndex)
{
    if(slides.length > 0) {

        if(slides.length > 1) {
            for (let i = 0; i < slides.length; ++i) {
                slides[i].style.display = "none";
            }
        }
        if(slidesVideos.length > 1) {
            for (let i = 0; i < slidesVideos.length; ++i) {
                slidesVideos[i].style.display = "none";
            }
        }

        if(slideIndex === slides.length) {
            if (slides.length === slidesVideos.length
                && (slides[0].childNodes[1].className === "videow"
                || slides[0].childNodes[1].className === "localCvideo")) {
                // Fin du diaporama vidéos classiques
                console.log("-Fin de l'affichage des vidéos.");
                return displayOrHide(slidesShow, 0);
            }
            // Afficher le diaporama des vidéos classiques
            else if (slidesVideos.length !== 0) {
                console.log("-Fin du diaporama - On affiche les vidéos");
                displayOrHide(slidesVideos, 0);
            }
            // Fin du diaporama des autres informations
            console.log("-Fin du diaporama - On recommence");
            slideIndex = 0;
            return;
        }

        // Check if the slide exist
        if(slides[slideIndex] !== undefined) {
            console.log("--Slide n°"+ slideIndex);
            console.log("timeout before = " + timeout);
            // Display the slide and reset timeout to its default value.
            slides[slideIndex].style.display = "block";
            // Check child
            if(slides[slideIndex].childNodes) {
                var count = 0;
                // Try to find if it's a PDF or a video
                for(let i = 0; i < slides[slideIndex].childNodes.length; ++i) {
                    console.log("Affichage slide index " + i + ": " + (slides[slideIndex].childNodes[i].className));
                    // If is a PDF
                    if (slides[slideIndex].childNodes[i].className === 'canvas_pdf') {

                        console.log("--Lecture de PDF");
                        timeout = 10000;
                        count = count + 1;

                        // Generate the url
                        let pdfLink = slides[slideIndex].childNodes[i].id;
                        pdfUrl = urlUpload + pdfLink;

                        let loadingTask = pdfjsLib.getDocument(pdfUrl);
                        loadingTask.promise.then(function (pdf) {

                            totalPage = pdf.numPages;
                            ++numPage;

                            let div = document.getElementById(pdfLink);
                            let scale = 1.5;

                            if (stop === false) {
                                if (document.getElementById('the-canvas-' + pdfLink + '-page' + (numPage - 1)) != null) {
                                    console.log('----Supression page n°' + (numPage - 1));
                                    document.getElementById('the-canvas-' + pdfLink + '-page' + (numPage - 1)).remove();
                                }
                            }

                            if (totalPage >= numPage && stop === false) {
                                pdf.getPage(numPage).then(function (page) {

                                    console.log(slides.length);
                                    console.log(totalPage);
                                    if (slides.length === 1 && totalPage === 1 || totalPage === null && slides.length === 1) {
                                        stop = true;
                                    }

                                    console.log(stop);

                                    console.log("---Page du PDF n°" + numPage);

                                    let viewport = page.getViewport({scale: scale,});

                                    $('<canvas >', {
                                        id: 'the-canvas-' + pdfLink + '-page' + numPage,
                                    }).appendTo(div).fadeOut(0).fadeIn(2000);

                                    let canvas = document.getElementById('the-canvas-' + pdfLink + '-page' + numPage);
                                    let context = canvas.getContext('2d');
                                    canvas.height = viewport.height;
                                    canvas.width = viewport.width;

                                    let renderContext = {
                                        canvasContext: context,
                                        viewport: viewport
                                    };

                                    // Give the CSS to the canvas
                                    if (slides === document.getElementsByClassName("mySlides")) {
                                        canvas.style.maxHeight = "99vh";
                                        canvas.style.maxWidth = "100%";
                                        canvas.style.height = "99vh";
                                        canvas.style.width = "auto";
                                    } else {
                                        canvas.style.maxHeight = "68vh";
                                        canvas.style.maxWidth = "100%";
                                        canvas.style.height = "auto";
                                        canvas.style.width = "auto";
                                    }

                                    page.render(renderContext);
                                });

                                if (numPage === totalPage) {
                                    // Reinitialise variables
                                    if (stop === false) {
                                        if (document.getElementById('the-canvas-' + pdfLink + '-page' + (totalPage)) != null) {
                                            document.getElementById('the-canvas-' + pdfLink + '-page' + (totalPage)).remove();
                                        }
                                    }
                                    console.log("--Fin du PDF");
                                    totalPage = null;
                                    numPage = 0;
                                    endPage = true;
                                    ++slideIndex;
                                    // Go to the next slide
                                }
                            }
                        });
                    }
                    /*
                    * Si c'est une vidéo,
                    * ⇒ différents traitements en fonction du type de la vidéo
                    * */
                    //If it's a YouTube video
                    if (slides[slideIndex].childNodes[i].className === 'videosh' || slides[slideIndex].childNodes[i].className === 'videow') {
                        const listeVideosYouTube = document.querySelectorAll(".videosh, .videow");
                        for (let indexVideoYouTube = 0; indexVideoYouTube < listeVideosYouTube.length; ++indexVideoYouTube) {
                            let videoYouTube = listeVideosYouTube[indexVideoYouTube];

                            // Si la vidéo correspond à celle affichée, on associe un id pour l'API
                            if (videoYouTube === slides[slideIndex].childNodes[i]) {
                                videoYouTube.id = "videoID";
                                urlYoutube = videoYouTube.src;
                                let lastElementIndex = 0;
                                for (let i = 0; i < urlYoutube.length; ++i) {
                                    if (urlYoutube.charAt(i) === "?") {
                                        lastElementIndex = i;
                                        break;
                                    }
                                }
                                urlYoutube = urlYoutube.substring(30, lastElementIndex);
                            }
                            // Sinon, elle n'a pas l'id
                            else {
                                videoYouTube.id = "";
                            }
                        }
                        onYouTubeIframeAPIReady();
                    }
                    // If it's a local normal video
                    else if (slides[slideIndex].childNodes[i].className === 'localCvideo') {
                        console.log("--Lecture vidéo locale classique");
                        const listeVideosClassiqueLocal = document.querySelectorAll(".localCvideo");

                        //Chargement de toutes les vidéos de même classe HTML
                        for (let indexVideoClassiqueLocal = 0; indexVideoClassiqueLocal < listeVideosClassiqueLocal.length; ++indexVideoClassiqueLocal) {
                            let videoClassiqueLocal = listeVideosClassiqueLocal[indexVideoClassiqueLocal];

                            //Si la vidéo correspond à celle affichée
                            if (listeVideosClassiqueLocal[indexVideoClassiqueLocal] === slides[slideIndex].childNodes[i]) {
                                timeout = videoClassiqueLocal.duration * 1000;
                                console.log("timeout = " + timeout + "\tduration = " + videoClassiqueLocal.duration);
                            }

                            //Chargement et lecture de la vidéo
                            videoClassiqueLocal.load();
                            videoClassiqueLocal.currentTime = 0;
                            videoClassiqueLocal.play();

                            //Évènement la vidéo est terminée
                            videoClassiqueLocal.onended = () => {
                                videoClassiqueLocal.pause();
                                videoClassiqueLocal.currentTime = 0;
                            }
                        }
                    }
                    // If it's a local short video
                    else if (slides[slideIndex].childNodes[i].className === 'localSvideo') {
                        console.log("--Lecture vidéo locale short");
                        const listeVideosShortLocal = document.querySelectorAll(".localSvideo");

                        //Chargement de toutes les vidéos de même classe HTML
                        for (let indexVideoShortLocal = 0; indexVideoShortLocal < listeVideosShortLocal.length; ++indexVideoShortLocal) {
                            let videoShortLocal = listeVideosShortLocal[indexVideoShortLocal];

                            //Si la vidéo correspond à celle affichée
                            if (listeVideosShortLocal[indexVideoShortLocal] === slides[slideIndex].childNodes[i]) {
                                timeout = videoShortLocal.duration * 1000;
                                console.log("timeout = " + timeout + "\tduration = " + videoShortLocal.duration);
                            }

                            //Chargement de la vidéo
                            videoShortLocal.load();
                            videoShortLocal.currentTime = 0;
                            videoShortLocal.play();

                            //Évènement la vidéo est terminée
                            videoShortLocal.onended = () => {
                                videoShortLocal.pause();
                                videoShortLocal.currentTime = 0;
                            }
                        }
                    }
                    // If it's an image
                    else if (slides[slideIndex].childNodes[i].className === 'img-thumbnail') {
                        console.log('--Lecture image');
                        timeout = 10000;
                    }
                    // If it's an image
                    else if (slides[slideIndex].childNodes[i].className === 'text-info') {
                        console.log('--Lecture texte');
                        timeout = 10000;
                    }
                }
                if (count === 0) {
                    console.log("--Lecture divers");
                    // Go to the next slide
                    ++slideIndex;
                }
            } else {
                // Go to the next slide
                ++slideIndex;
            }
            console.log("Slide " + slideIndex + " sur " + slides.length);
        }
    }

    if(slides.length !== 1 || totalPage !== 1) {
        console.log("Real timeout = " + timeout);
        setTimeout(function(){displayOrHide(slides, slideIndex)} , timeout);
    }
}

//TODO fonctions API Youtube
/**
 * Fonction qui démarre l'API et attribue les valeurs aux lecteurs de vidéos.
 * */
function onYouTubeIframeAPIReady() {
    console.log("---API Youtube démarrée");
    player = new YT.Player('videoID', {
        events: {
            onReady : onPlayerReady,
            onStateChange: onPlayerStateChange
        }
    });
}

/**
 * Fonction évènementielle qui s'active lors de l'évènement onReady: s'active lorsque le lecteur concerné est chargé.
 * */

function onPlayerReady(event) {
    event.target.cueVideoById(urlYoutube, 0, "default");
    console.log("Chargement vidéo");
    console.log(event.target.getVideoUrl());
    event.target.seekTo(0);
    event.target.playVideo();
    timeout = (event.target.getDuration() + 1) * 1000;
    console.log("timeout YT = " + timeout);

}

/**
 * Fonction évènementielle qui s'active lors de l'évènement onStateChange: se déclenche lorsque l'état du lecteur concerné change.
 * */

function onPlayerStateChange(event) {
    if (event.data === -1) {
        event.target.seekTo(0);
        event.target.playVideo();
        timeout = (event.target.getDuration() + 1) * 1000;
        console.log("timeout YT = " + timeout + "\tduration = " + event.target.getDuration());
    }
    if (event.data === YT.PlayerState.ENDED) {
        console.log("Vidéo terminée");
        reloadVideo(event.target);
    }
}

function reloadVideo(player) {
    player.seekTo(0);
    player.playVideo();
}
