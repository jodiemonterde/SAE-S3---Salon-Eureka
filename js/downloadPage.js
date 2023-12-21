const downloadUp = document.getElementById("downloadUp");
const downloadDown = document.getElementById("downloadDown");
downloadUp.addEventListener("click", downloadAsPdf);
downloadDown.addEventListener("click", downloadAsPdf);

function downloadAsPdf() {
    const element = document.getElementById('toPrint');
    html = html2pdf().from(element).save();
}