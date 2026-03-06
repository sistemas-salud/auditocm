let ultimoValor = null;
let ultimoEsFactura = null;

function formatCurrency(value) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP'
    }).format(value || 0);
}

function verDetalle(valor, esFactura) {

    ultimoValor = valor;
    ultimoEsFactura = esFactura;

    let url;

    if (esFactura) {
        url = `/cartera/detalle?no_factura=${encodeURIComponent(valor)}`;
    } else {
        url = `/cartera/detalle_excel?acta=${encodeURIComponent(ultimoValor)}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {

            let tbody = document.querySelector("#tablaDetalle tbody");
            tbody.innerHTML = "";

            data.data.forEach((row, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${row.no_factura}</td>
                        <td>${row.nit}</td>
                        <td>${row.ips}</td>
                        <td>${formatCurrency(row.VALOR_FACTURA)}</td>
                        <td>${formatCurrency(row.VALOR_GLOSADO)}</td>
                        <td>${formatCurrency(row.VALOR_RECONOCIDO)}</td>
                        <td>${formatCurrency(row.VALOR_PAGADO)}</td>
                        <td>${formatCurrency(row.VALOR_SALDO)}</td>
                    </tr>
                `;
            });

            document.getElementById("totalDetalle").innerText = data.data.length;

            let modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
            modal.show();
        });
}

function descargarExcel() {

    if (!ultimoValor) return;

    let url;

    if (ultimoEsFactura) {
        url = `/cartera/detalle_excel?no_factura=${encodeURIComponent(ultimoValor)}`;
    } else {
        url = `/cartera/detalle_excel?nit=${encodeURIComponent(ultimoValor)}`;
    }

    window.location.href = url;
}