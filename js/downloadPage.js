const downloadUp = document.getElementById("downloadUp");
const downloadDown = document.getElementById("downloadDown");
downloadUp.addEventListener("click", downloadAsPdf);
downloadDown.addEventListener("click", downloadAsPdf);

function downloadAsPdf() {
    // Choose the element that your content will be rendered to.
    const element = document.getElementById('toPrint');
    // Choose the element and save the PDF for your user.
    html = html2pdf().from(element).save();
}