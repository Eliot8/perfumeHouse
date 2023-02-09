var modal = document.getElementById('img-modal');

var modalClose = document.getElementById('img-modal-close');
modalClose.addEventListener('click', function() {
    modal.style.display = "none";
});

document.addEventListener('click', function(e) {
    if (e.target.className.indexOf('img-modal-target') !== -1) {
        var img = e.target;
        var modalImg = document.getElementById("img-modal-content");
        var captionText = document.getElementById("img-modal-caption");
        modal.style.display = "block";
        modalImg.src = img.src;
        captionText.innerHTML = img.alt;
    }
});