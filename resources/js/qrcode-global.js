import QRCode from 'qrcode';
window.generateQR = (canvas, text) => {
    QRCode.toCanvas(canvas, text, {
        width: 200,
        margin: 2,
        color: { dark: '#000000', light: '#ffffff' },
        errorCorrectionLevel: 'H',
    });
};
