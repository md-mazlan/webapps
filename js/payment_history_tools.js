// Payment History PDF & Print Utility
// Requires html2pdf CDN for PDF download
function downloadPaymentHistoryPDF() {
  const element = document.querySelector('.container');
  if (!window.html2pdf) {
    alert('PDF library not loaded.');
    return;
  }
  html2pdf().from(element).set({
    margin: 0.5,
    filename: 'payment_history.pdf',
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
  }).save();
}

function printPaymentHistory() {
  const printContents = document.querySelector('.container').outerHTML;
  const printWindow = window.open('', '', 'height=700,width=900');
  printWindow.document.write('<html><head><title>Print Receipt</title>');
  document.querySelectorAll('link[rel=stylesheet]').forEach(link => {
    printWindow.document.write(link.outerHTML);
  });
  printWindow.document.write('</head><body>');
  printWindow.document.write(printContents);
  printWindow.document.write('</body></html>');
  printWindow.document.close();
  printWindow.focus();
  setTimeout(() => printWindow.print(), 500);
}
