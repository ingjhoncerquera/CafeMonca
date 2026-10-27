<?php require_once __DIR__ . '/../layouts/header_cliente.php'; ?>

<?php

require_once __DIR__ . '/../../models/EmpresaModel.php';

$empresaModel = new EmpresaModel();
$empresa = $empresaModel->obtener();

date_default_timezone_set('America/Bogota');

$horaActual = date("H:i:s");

$abierto =
(
    $horaActual >= $empresa['horario_apertura'] &&
    $horaActual <= $empresa['horario_cierre']
)
||
(
    $horaActual >= $empresa['horario_apertura_tarde'] &&
    $horaActual <= $empresa['horario_cierre_tarde']
);

?>

<?php

$abierto = $empresaModel->restauranteAbierto();

?>

<!-- ============================================================
     POPUP PROMOCIÓN ACTIVA
============================================================ -->

<?php if (!empty($promocion)): ?>

<div
    class="modal fade"
    id="promocionModal"
    tabindex="-1"
    aria-labelledby="promocionModalLabel"
    aria-hidden="true">


<div class="modal-dialog modal-dialog-centered promocion-dialog">

    <div class="modal-content promocion-modal">


        <?php if (!empty($promocion['imagen'])): ?>

            <div class="promocion-imagen-container">

                <img
                    src="uploads/promociones/<?= htmlspecialchars(ltrim($promocion['imagen'], '/')) ?>"
                    alt="<?= htmlspecialchars($promocion['titulo']) ?>"
                    class="promocion-imagen"
                    onerror="this.parentElement.style.display='none';">

            </div>

        <?php endif; ?>


        <div class="promocion-contenido">


            <button
                type="button"
                class="btn-close promocion-cerrar"
                data-bs-dismiss="modal"
                aria-label="Cerrar">
            </button>


            <div class="promocion-etiqueta">

                PROMOCIÓN ESPECIAL

            </div>


            <h2
                id="promocionModalLabel"
                class="promocion-titulo">

                <?= htmlspecialchars($promocion['titulo']) ?>

            </h2>


            <?php if (!empty($promocion['descripcion'])): ?>

                <p class="promocion-descripcion">

                    <?= nl2br(
                        htmlspecialchars(
                            $promocion['descripcion']
                        )
                    ) ?>

                </p>

            <?php endif; ?>


            <?php if (!empty($promocion['enlace'])): ?>

                <a
                    href="<?= htmlspecialchars($promocion['enlace']) ?>"
                    class="btn promocion-boton"
                    target="_blank"
                    rel="noopener noreferrer">

                    <?= !empty($promocion['texto_boton'])
                        ? htmlspecialchars($promocion['texto_boton'])
                        : 'Ver promoción' ?>

                </a>

            <?php endif; ?>


            <div class="promocion-vigencia">

                <small>

                    Promoción válida del

                    <?= date(
                        'd/m/Y',
                        strtotime(
                            $promocion['fecha_inicio']
                        )
                    ) ?>

                    al

                    <?= date(
                        'd/m/Y',
                        strtotime(
                            $promocion['fecha_fin']
                        )
                    ) ?>

                </small>

            </div>


        </div>

    </div>

</div>


</div>

<script>

window.addEventListener("load", function(){

    const promocionModal =
        document.getElementById("promocionModal");


    if(!promocionModal){

        console.log(
            "No existe el elemento promocionModal."
        );

        return;

    }


    /*
     * Comprobamos que Bootstrap esté disponible.
     */

    if(
        typeof bootstrap === "undefined" ||
        typeof bootstrap.Modal === "undefined"
    ){

        console.error(
            "Bootstrap JavaScript no está cargado."
        );

        return;

    }


    const dialogo =
        promocionModal.querySelector(
            ".promocion-dialog"
        );


    const modalContenido =
        promocionModal.querySelector(
            ".promocion-modal"
        );


    const imagen =
        promocionModal.querySelector(
            ".promocion-imagen"
        );


    /*
     * ========================================================
     * AJUSTAR TAMAÑO DEL POPUP
     * ========================================================
     *
     * El popup toma como referencia el tamaño real
     * de la imagen.
     *
     * Si la imagen cabe en la pantalla:
     *
     *     Popup = ancho real de la imagen
     *
     * Si es demasiado grande:
     *
     *     La imagen se reduce proporcionalmente.
     *
     * De esta manera nunca se recorta.
     */

    function ajustarPromocion(){

        if(!dialogo || !modalContenido){

            return;

        }


        /*
         * Margen lateral disponible.
         */

        const margen =
            window.innerWidth <= 767
                ? 20
                : 40;


        /*
         * Ancho máximo permitido.
         */

        const anchoMaximo =
            window.innerWidth - margen;


        /*
         * Alto máximo permitido.
         *
         * Dejamos un pequeño margen para que
         * la promoción pueda verse completa.
         */

        const altoMaximo =
            window.innerHeight * 0.75;


        /*
         * ====================================================
         * CUANDO EXISTE IMAGEN
         * ====================================================
         */

        if(
            imagen &&
            imagen.naturalWidth > 0 &&
            imagen.naturalHeight > 0
        ){

            const anchoOriginal =
                imagen.naturalWidth;


            const altoOriginal =
                imagen.naturalHeight;


            /*
             * Calculamos cuánto debemos reducir
             * la imagen si es demasiado grande.
             */

            const escala =
                Math.min(
                    1,
                    anchoMaximo / anchoOriginal,
                    altoMaximo / altoOriginal
                );


            /*
             * Tamaño final.
             */

            const anchoFinal =
                Math.round(
                    anchoOriginal * escala
                );


            const altoFinal =
                Math.round(
                    altoOriginal * escala
                );


            /*
             * El diálogo toma exactamente
             * el ancho de la imagen.
             */

            dialogo.style.setProperty(
                "width",
                anchoFinal + "px",
                "important"
            );


            dialogo.style.setProperty(
                "max-width",
                anchoMaximo + "px",
                "important"
            );


            /*
             * El contenido tiene exactamente
             * el mismo ancho.
             */

            modalContenido.style.setProperty(
                "width",
                anchoFinal + "px",
                "important"
            );


            modalContenido.style.setProperty(
                "max-width",
                "100%",
                "important"
            );


            /*
             * La imagen recibe su tamaño calculado.
             */

            imagen.style.setProperty(
                "width",
                anchoFinal + "px",
                "important"
            );


            imagen.style.setProperty(
                "height",
                altoFinal + "px",
                "important"
            );


            /*
             * Quitamos restricciones que puedan
             * provocar que Bootstrap o CSS la reduzcan
             * de manera incorrecta.
             */

            imagen.style.setProperty(
                "max-width",
                "none",
                "important"
            );


            imagen.style.setProperty(
                "max-height",
                "none",
                "important"
            );

        }else{

            /*
             * =================================================
             * SIN IMAGEN
             * =================================================
             *
             * Si por alguna razón la promoción no tiene
             * una imagen válida, usamos un ancho moderado.
             */

            const anchoSinImagen =
                Math.min(
                    460,
                    anchoMaximo
                );


            dialogo.style.setProperty(
                "width",
                anchoSinImagen + "px",
                "important"
            );


            dialogo.style.setProperty(
                "max-width",
                anchoMaximo + "px",
                "important"
            );


            modalContenido.style.setProperty(
                "width",
                anchoSinImagen + "px",
                "important"
            );

        }

    }


    /*
     * ========================================================
     * ESPERAR A QUE CARGUE LA IMAGEN
     * ========================================================
     */

    if(imagen){

        if(imagen.complete){

            ajustarPromocion();

        }else{

            imagen.addEventListener(
                "load",
                ajustarPromocion,
                {
                    once:true
                }
            );

        }

    }else{

        ajustarPromocion();

    }


    try{

        const modal =
            new bootstrap.Modal(
                promocionModal,
                {
                    backdrop:true,
                    keyboard:true
                }
            );


        /*
         * Ajustamos antes de mostrar.
         */

        ajustarPromocion();


        /*
         * Mostramos popup.
         */

        modal.show();


        /*
         * Si el usuario cambia el tamaño de la
         * ventana, recalculamos el popup.
         */

        window.addEventListener(
            "resize",
            ajustarPromocion
        );


        console.log(
            "Popup de promoción mostrado correctamente."
        );


    }catch(error){

        console.error(
            "Error al abrir el popup:",
            error
        );

    }

});

</script>

<?php endif; ?>

<link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap"
    rel="stylesheet">

<!-- ============================================================
     ENCABEZADO
============================================================ -->

<div class="d-flex align-items-center justify-content-between mb-3 encabezado-menu">


<button
    class="btn btn-dark btn-menu"
    data-bs-toggle="offcanvas"
    data-bs-target="#menuCategorias">

    ☰

</button>


<h4 class="m-0 fw-bold titulo-menu">

    Nuestro Menú - Café Monca

</h4>


<div class="espacio-header"></div>


</div>

<!-- ============================================================
     ESTADO DEL RESTAURANTE
============================================================ -->

<?php if($abierto): ?>

<div class="alert alert-success text-center mb-3 estado-restaurante">


<strong>🟢 Café Monca Abierto</strong>

<br>

<small>

    Horario:

    <?= date(
        "g:i a",
        strtotime($empresa['horario_apertura'])
    ) ?>

    -

    <?= date(
        "g:i a",
        strtotime($empresa['horario_cierre'])
    ) ?>

    &nbsp; | &nbsp;

    <?= date(
        "g:i a",
        strtotime($empresa['horario_apertura_tarde'])
    ) ?>

    -

    <?= date(
        "g:i a",
        strtotime($empresa['horario_cierre_tarde'])
    ) ?>

</small>


</div>

<?php else: ?>

<div class="alert alert-warning text-center mb-3 estado-restaurante">


<strong>🔴 Café Monca Cerrado</strong>

<br>

<small>

    Nuestro horario es:

    <?= date(
        "g:i a",
        strtotime($empresa['horario_apertura'])
    ) ?>

    -

    <?= date(
        "g:i a",
        strtotime($empresa['horario_cierre'])
    ) ?>

    &nbsp; | &nbsp;

    <?= date(
        "g:i a",
        strtotime($empresa['horario_apertura_tarde'])
    ) ?>

    -

    <?= date(
        "g:i a",
        strtotime($empresa['horario_cierre_tarde'])
    ) ?>

</small>


</div>

<?php endif; ?>

<!-- ============================================================
     MENÚ LATERAL DE CATEGORÍAS
============================================================ -->

<div
    class="offcanvas offcanvas-start"
    tabindex="-1"
    id="menuCategorias">


<div class="offcanvas-header">

    <h5>
        Categorías
    </h5>


    <button
        class="btn-close"
        data-bs-dismiss="offcanvas">
    </button>

</div>


<div class="offcanvas-body">


    <button
        class="btn btn-outline-primary w-100 mb-2 filtro-btn active"
        data-categoria="all">

        Todos

    </button>


    <?php foreach($categorias as $cat): ?>

        <button
            class="btn btn-outline-primary w-100 mb-2 filtro-btn"
            data-categoria="<?= $cat['id_categoria'] ?>">

            <?= htmlspecialchars($cat['nombre']) ?>

        </button>

    <?php endforeach; ?>


</div>


</div>

<!-- ============================================================
     CONTENEDOR PRINCIPAL
============================================================ -->

<div class="container mt-4 contenedor-menu">


<!-- ========================================================
     CATEGORÍAS SUPERIORES
========================================================= -->

<div class="mb-4 categorias-menu">


    <button
        class="btn btn-outline-primary filtro-btn active"
        data-categoria="all">

        Todos

    </button>


    <?php foreach($categorias as $cat): ?>

        <button
            class="btn btn-outline-primary filtro-btn"
            data-categoria="<?= $cat['id_categoria'] ?>">

            <?= htmlspecialchars($cat['nombre']) ?>

        </button>

    <?php endforeach; ?>


</div>


<!-- ========================================================
     PRODUCTOS
========================================================= -->

<div class="row productos-grid">


    <?php foreach($productos as $p):

        $imagen = !empty($p['imagen'])
            ? 'uploads/'.$p['imagen']
            : 'https://via.placeholder.com/300x200';

    ?>


    <div
        class="col-6 col-md-4 mb-4 producto-item"
        data-categoria="<?= $p['id_categoria'] ?>">


        <!-- =================================================
             TARJETA PRODUCTO
        ================================================= -->

        <div
            class="card producto-card"
            role="button"
            tabindex="0"
            aria-label="Ver detalles de <?= htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8') ?>"
            data-producto-id="<?= (int)$p['id_producto'] ?>"
            data-producto-nombre="<?= htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8') ?>"
            data-producto-descripcion="<?= htmlspecialchars(trim($p['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
            data-producto-precio="<?= (float)$p['precio'] ?>"
            data-producto-imagen="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>"
            data-producto-disponible="<?= $abierto ? '1' : '0' ?>"
            onclick="abrirProducto(this)"
            onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();abrirProducto(this)}">


            <!-- =================================================
                 FOTO
            ================================================= -->

            <div class="img-container">


                <img
                    src="<?= $imagen ?>"
                    alt="<?= htmlspecialchars($p['nombre']) ?>">


            </div>


            <!-- =================================================
                 INFORMACIÓN DEL PRODUCTO
                 YA NO ESTÁ SOBRE LA FOTO
            ================================================= -->

            <div class="info-producto">


                <h5 class="nombre-producto">


                    <?= htmlspecialchars($p['nombre']) ?>


                </h5>


                <p class="descripcion-producto">


                    <?php

                    $desc = isset($p['descripcion'])
                        ? trim($p['descripcion'])
                        : '';


                    echo $desc != ''
                        ? htmlspecialchars($desc)
                        : 'Delicioso producto preparado con ingredientes frescos 👨‍🍳';


                    ?>


                </p>


                <div class="precio-producto">


                    $<?= number_format(
                        $p['precio'],
                        0,
                        ',',
                        '.'
                    ) ?>


                </div>


            </div>


            <!-- =================================================
                 BOTÓN AGREGAR
            ================================================= -->

            <?php if($abierto){ ?>


                <button
                    class="btn-add"
                    onclick="event.stopPropagation(); agregarPedido(<?= $p['id_producto'] ?>,'<?= htmlspecialchars($p['nombre']) ?>',<?= $p['precio'] ?>)">


                    +


                </button>


            <?php }else{ ?>


                <button
                    class="btn btn-danger w-100 btn-cerrado"
                    disabled>


                    🚫 Cerrado


                </button>


            <?php } ?>


        </div>


    </div>


    <?php endforeach; ?>


</div>


</div>

<!-- MODAL DETALLE DE PRODUCTO -->
<div class="modal fade" id="productoDetalleModal" tabindex="-1" aria-labelledby="productoDetalleTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable producto-detalle-dialog">
        <div class="modal-content producto-detalle-modal">
            <div class="producto-detalle-imagen-wrap">
                <img id="productoDetalleImagen" src="" alt="" class="producto-detalle-imagen">
                <button type="button" class="btn-close producto-detalle-cerrar" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body producto-detalle-cuerpo">
                <h2 id="productoDetalleTitulo" class="producto-detalle-titulo"></h2>
                <p id="productoDetalleDescripcion" class="producto-detalle-descripcion"></p>
                <div class="producto-detalle-pie">
                    <strong id="productoDetallePrecio" class="producto-detalle-precio"></strong>
                    <button type="button" id="productoDetalleAgregar" class="btn producto-detalle-agregar" onclick="agregarProductoDesdeDetalle()">Agregar al pedido</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="height:100px;"></div>

<!-- ============================================================
     BOTÓN CARRITO
============================================================ -->

<button
 class="btn-carrito"
 data-bs-toggle="offcanvas"
 data-bs-target="#carritoCanvas">


🛒


<span id="contador">

    0

</span>


</button>

<!-- ============================================================
     CARRITO
============================================================ -->

<div
    class="offcanvas offcanvas-end"
    tabindex="-1"
    id="carritoCanvas">


<div class="offcanvas-header">


    <h5>

        Tu Pedido

    </h5>


    <button
        class="btn-close"
        data-bs-dismiss="offcanvas">
    </button>


</div>


<div class="offcanvas-body">


    <ul
        id="listaCarrito"
        class="list-group mb-3">
    </ul>


    <h5>


        Total:


        $<span id="total">

            0

        </span>


    </h5>


    <button
        onclick="abrirModal()"
        class="btn btn-success w-100">


        📲 Enviar pedido


    </button>


</div>


</div>

<!-- ============================================================
     MODAL CLIENTE
============================================================ -->

<div
    class="modal fade"
    id="clienteModal"
    tabindex="-1">


<div class="modal-dialog">


    <div class="modal-content">


        <div class="modal-header">


            <h5 class="modal-title">

                Ingresa datos para tu pedido

            </h5>


            <button
                class="btn-close"
                data-bs-dismiss="modal">
            </button>


        </div>


        <div class="modal-body">


            <input
                type="text"
                id="nombreCliente"
                class="form-control mb-2"
                placeholder="Nombre completo">


            <input
                type="email"
                id="emailCliente"
                class="form-control mb-2"
                placeholder="Correo electrónico">


            <input
                type="text"
                id="telefonoCliente"
                class="form-control mb-2"
                placeholder="Teléfono">


            <input
                type="text"
                id="direccionCliente"
                class="form-control"
                placeholder="Dirección">


        </div>


        <div class="modal-footer">


            <button
                class="btn btn-success"
                onclick="confirmarPedido()">


                Confirmar pedido


            </button>


        </div>


    </div>


</div>


</div>

<!-- ============================================================
     MODAL CONFIRMAR PEDIDO
============================================================ -->

<div
    class="modal fade"
    id="confirmarModal"
    tabindex="-1">

<div class="modal-dialog modal-dialog-centered">


    <div class="modal-content">


        <div class="modal-header">


            <h5 class="modal-title">

                Confirmar pedido

            </h5>


            <button
                class="btn-close"
                data-bs-dismiss="modal">
            </button>


        </div>


        <div class="modal-body">


            <p>

                <strong>

                    ¿Deseas enviar tu pedido?

                </strong>

            </p>


            <p
                id="resumenPedido"
                style="font-size:14px;">
            </p>


        </div>


        <div class="modal-footer">


            <button
                class="btn btn-secondary"
                data-bs-dismiss="modal">


                Cancelar


            </button>


            <button
                class="btn btn-success"
                onclick="procesarPedido()">


                Sí, enviar


            </button>


        </div>


    </div>


</div>


</div>

<!-- ============================================================
     MODAL ALERTA
============================================================ -->

<div
    id="modalAlertaCustom"
    class="modal-alerta-custom">


<div class="contenido-alerta">


    <h4>

        ⚠️ Atención

    </h4>


    <p id="textoModalCustom"></p>


    <button
        onclick="cerrarModalCustom()">


        OK


    </button>


</div>


</div>

<script>

const VALOR_DOMICILIO =
    <?= $empresa['domicilio'] ?? 0 ?>;

</script>

<!-- ============================================================
     JAVASCRIPT
============================================================ -->

<script>

let carrito = [];
let productoDetalleSeleccionado = null;

function abrirProducto(elemento){
    const d = elemento.dataset;
    productoDetalleSeleccionado = {
        id: Number(d.productoId),
        nombre: d.productoNombre || '',
        precio: Number(d.productoPrecio || 0),
        disponible: d.productoDisponible === '1'
    };
    const imagen = document.getElementById('productoDetalleImagen');
    imagen.src = d.productoImagen || '';
    imagen.alt = d.productoNombre || 'Producto';
    document.getElementById('productoDetalleTitulo').textContent = d.productoNombre || '';
    document.getElementById('productoDetalleDescripcion').textContent =
        d.productoDescripcion || 'Delicioso producto preparado con ingredientes frescos.';
    document.getElementById('productoDetallePrecio').textContent =
        String.fromCharCode(36) + productoDetalleSeleccionado.precio.toLocaleString('es-CO', {maximumFractionDigits: 0});
    const boton = document.getElementById('productoDetalleAgregar');
    boton.disabled = !productoDetalleSeleccionado.disponible;
    boton.textContent = productoDetalleSeleccionado.disponible ? 'Agregar al pedido' : 'Restaurante cerrado';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('productoDetalleModal')).show();
}

function agregarProductoDesdeDetalle(){
    if(!productoDetalleSeleccionado || !productoDetalleSeleccionado.disponible) return;
    agregarPedido(productoDetalleSeleccionado.id, productoDetalleSeleccionado.nombre, productoDetalleSeleccionado.precio);
    bootstrap.Modal.getOrCreateInstance(document.getElementById('productoDetalleModal')).hide();
}


/* ============================================================
   AGREGAR PRODUCTO
============================================================ */

function agregarPedido(id, nombre, precio){

    let item =
        carrito.find(p => p.id === id);


    if(item){

        item.cantidad++;

    }else{

        carrito.push({
            id,
            nombre,
            precio,
            cantidad:1
        });

    }


    actualizarCarrito();


    let notif =
        document.createElement('div');


    notif.innerHTML =
        `✓ <strong>${nombre}</strong> agregado al carrito`;


    notif.className =
        'notificacion-carrito';


    document.body.appendChild(notif);


    setTimeout(
        () => notif.remove(),
        2500
    );

}


/* ============================================================
   AUMENTAR
============================================================ */

function aumentarCantidad(id){

    let item =
        carrito.find(p => p.id === id);


    if(item){

        item.cantidad++;

        actualizarCarrito();

    }

}


/* ============================================================
   DISMINUIR
============================================================ */

function disminuirCantidad(id){

    let item =
        carrito.find(p => p.id === id);


    if(!item) return;


    item.cantidad--;


    if(item.cantidad <= 0){

        carrito =
            carrito.filter(p => p.id !== id);

    }


    actualizarCarrito();

}


/* ============================================================
   ELIMINAR
============================================================ */

function eliminarProducto(id){

    carrito =
        carrito.filter(p => p.id !== id);


    actualizarCarrito();

}


/* ============================================================
   ACTUALIZAR CARRITO
============================================================ */

function actualizarCarrito(){

    let lista =
        document.getElementById("listaCarrito");


    let subtotal = 0;


    let contador = 0;


    lista.innerHTML = "";


    if(carrito.length == 0){

        lista.innerHTML = `

            <li class="list-group-item text-center text-muted">

                🛒 El carrito está vacío

            </li>

        `;


        document.getElementById("total").innerText =
            "0";


        document.getElementById("contador").innerText =
            "0";


        return;

    }


    carrito.forEach(p => {

        subtotal +=
            p.precio * p.cantidad;


        contador +=
            p.cantidad;


        lista.innerHTML += `

        <li class="list-group-item">


            <div class="d-flex justify-content-between">


                <div>


                    <strong>
                        ${p.nombre}
                    </strong>


                    <br>


                    <small class="text-muted">

                        $${p.precio.toLocaleString('es-CO')}

                    </small>


                </div>


                <div>


                    <strong class="text-success">

                        $${(
                            p.precio *
                            p.cantidad
                        ).toLocaleString('es-CO')}

                    </strong>


                </div>


            </div>


            <div class="mt-2 d-flex justify-content-between align-items-center">


                <div class="btn-group btn-group-sm">


                    <button
                        class="btn btn-outline-danger"
                        onclick="disminuirCantidad(${p.id})">


                        -


                    </button>


                    <button
                        class="btn btn-light"
                        disabled>


                        ${p.cantidad}


                    </button>


                    <button
                        class="btn btn-outline-success"
                        onclick="aumentarCantidad(${p.id})">


                        +


                    </button>


                </div>


                <button
                    class="btn btn-outline-danger btn-sm"
                    onclick="eliminarProducto(${p.id})">


                    🗑


                </button>


            </div>


        </li>

        `;

    });


    let domicilio =
        VALOR_DOMICILIO;


    let totalGeneral =
        subtotal + domicilio;


    lista.innerHTML += `

        <li class="list-group-item">


            <div class="d-flex justify-content-between">


                <span>
                    Subtotal
                </span>


                <strong>

                    $${subtotal.toLocaleString('es-CO')}

                </strong>


            </div>


            <div class="d-flex justify-content-between">


                <span>

                    🛵 Domicilio

                </span>


                <strong>

                    $${domicilio.toLocaleString('es-CO')}

                </strong>


            </div>


            <hr>


            <div class="d-flex justify-content-between">


                <strong>

                    TOTAL

                </strong>


                <strong class="text-success fs-5">


                    $${totalGeneral.toLocaleString('es-CO')}


                </strong>


            </div>


        </li>


        <div class="d-grid mt-3">


            <button
                class="btn btn-danger"
                onclick="if(confirm('¿Vaciar carrito?')){carrito=[];actualizarCarrito();}">


                🗑 Vaciar carrito


            </button>


        </div>

    `;


    document.getElementById("total").innerText =
        totalGeneral.toLocaleString('es-CO');


    document.getElementById("contador").innerText =
        contador;

}


/* ============================================================
   ABRIR MODAL
============================================================ */

function abrirModal(){

    if(carrito.length === 0){

        mostrarModalCustom(
            "El carrito de pedidos está vacío"
        );

        return;

    }


    let modal =
        new bootstrap.Modal(
            document.getElementById('clienteModal')
        );


    modal.show();

}


/* ============================================================
   ALERTA
============================================================ */

function mostrarModalCustom(mensaje){

    document.getElementById(
        "textoModalCustom"
    ).innerText = mensaje;


    document.getElementById(
        "modalAlertaCustom"
    ).style.display = "flex";

}


/* ============================================================
   CERRAR ALERTA
============================================================ */

function cerrarModalCustom(){

    document.getElementById(
        "modalAlertaCustom"
    ).style.display = "none";

}


/* ============================================================
   CONFIRMAR PEDIDO
============================================================ */

function confirmarPedido(){

    let nombre =
        document.getElementById(
            "nombreCliente"
        ).value.trim();


    let telefono =
        document.getElementById(
            "telefonoCliente"
        ).value.trim();


    let direccion =
        document.getElementById(
            "direccionCliente"
        ).value.trim();


    let total =
        document.getElementById(
            "total"
        ).innerText;


    if(nombre === ""){

        mostrarModalCustom(
            "⚠️ Debe ingresar el nombre del cliente."
        );


        document.getElementById(
            "nombreCliente"
        ).focus();


        return;

    }


    if(telefono === ""){

        mostrarModalCustom(
            "⚠️ Debe ingresar el número de teléfono."
        );


        document.getElementById(
            "telefonoCliente"
        ).focus();


        return;

    }


    if(direccion === ""){

        mostrarModalCustom(
            "⚠️ Debe ingresar la dirección de entrega."
        );


        document.getElementById(
            "direccionCliente"
        ).focus();


        return;

    }


    let resumen = "";


    carrito.forEach(p => {

        resumen +=
            `${p.nombre} x${p.cantidad} — $${(
                p.precio *
                p.cantidad
            ).toLocaleString('es-CO')}<br>`;

    });


    resumen +=
        `<hr><strong>Total: $${total}</strong>`;


    resumen +=
        `<br><small>${nombre} - ${telefono}</small>`;


    resumen +=
        `<br><small>${direccion}</small>`;


    document.getElementById(
        "resumenPedido"
    ).innerHTML = resumen;


    let modal =
        new bootstrap.Modal(
            document.getElementById(
                'confirmarModal'
            )
        );


    modal.show();

}


/* ============================================================
   PROCESAR PEDIDO
============================================================ */

function procesarPedido(){

    let nombre =
        document.getElementById(
            "nombreCliente"
        ).value;


    let email =
        document.getElementById(
            "emailCliente"
        ).value;


    let telefono =
        document.getElementById(
            "telefonoCliente"
        ).value;


    let direccion =
        document.getElementById(
            "direccionCliente"
        ).value;


    let totalTexto =
        document.getElementById(
            "total"
        ).innerText;


    let total =
        totalTexto.replace(/\./g, '');


    let subtotal = 0;


    carrito.forEach(p => {

        subtotal +=
            p.precio * p.cantidad;

    });


    let domicilio =
        VALOR_DOMICILIO;


    let fecha =
        new Date().toLocaleString(
            'es-CO',
            {
                dateStyle:'full',
                timeStyle:'short'
            }
        );


    fetch(
        "index.php?controller=pedido&action=guardar",
        {
            method:"POST",

            headers:{
                "Content-Type":
                    "application/json"
            },

            body:JSON.stringify({

                carrito:carrito,

                total:total,

                nombre:nombre,

                email:email,

                telefono:telefono,

                direccion:direccion

            })

        }
    )


    .then(res => res.json())


    .then(data => {


        if(data.status === "ok"){


            let numeroPedido =
                data.id_pedido;


            let mensaje = "";


            mensaje +=
                `*NUEVO PEDIDO* #${numeroPedido} — Cafe Monca\n`;


            mensaje +=
                `━━━━━━━━━━━━━━━━\n\n`;


            mensaje +=
                `*DETALLE DEL PEDIDO:*\n`;


            carrito.forEach((p,i) => {


                let subtotalProducto =
                    (
                        p.precio *
                        p.cantidad
                    ).toLocaleString(
                        'es-CO'
                    );


                mensaje +=
                    `${i+1}. ${p.nombre}\n`;


                mensaje +=
                    `   • Cantidad: ${p.cantidad}\n`;


                mensaje +=
                    `   • Subtotal: $${subtotalProducto}\n\n`;

            });


            mensaje +=
                `━━━━━━━━━━━━━━━\n`;


            mensaje +=
                `*SUBTOTAL:* $${subtotal.toLocaleString('es-CO')}\n`;


            mensaje +=
                `*DOMICILIO:* $${domicilio.toLocaleString('es-CO')}\n`;


            mensaje +=
                `*TOTAL:* $${Number(total).toLocaleString('es-CO')}\n`;


            mensaje +=
                `━━━━━━━━━━━━━━━\n\n`;


            mensaje +=
                `*DATOS DEL CLIENTE:*\n`;


            mensaje +=
                `• Nombre: ${nombre}\n`;


            mensaje +=
                `• Teléfono: ${telefono}\n`;


            if(email)
                mensaje +=
                    `• Email: ${email}\n`;


            if(direccion)
                mensaje +=
                    `• Dirección: ${direccion}\n`;


            mensaje +=
                `\n*FECHA Y HORA:*\n`;


            mensaje +=
                `• ${fecha}\n\n`;


            mensaje +=
                `━━━━━━━━━━━━━━━\n`;


            mensaje +=
                `_Pedido realizado desde la app_`;


            let mensajeCodificado =
                encodeURIComponent(
                    mensaje
                );


            bootstrap.Modal
                .getInstance(
                    document.getElementById(
                        'clienteModal'
                    )
                )
                .hide();


            bootstrap.Modal
                .getInstance(
                    document.getElementById(
                        'confirmarModal'
                    )
                )
                .hide();


            let modalExito =
                document.createElement(
                    'div'
                );


            modalExito.innerHTML = `

                <div
                    class="modal fade"
                    id="exitoModal"
                    tabindex="-1"
                    data-bs-backdrop="static">


                    <div
                        class="modal-dialog modal-dialog-centered">


                        <div
                            class="modal-content border-0 shadow-lg modal-exito">


                            <div
                                class="modal-body text-center p-5">


                                <div class="icono-exito mb-3">


                                    <svg
                                        viewBox="0 0 52 52"
                                        width="80"
                                        height="80">


                                        <circle
                                            cx="26"
                                            cy="26"
                                            r="25"
                                            fill="none"
                                            stroke="#5c4033"
                                            stroke-width="2"
                                            class="circulo"/>


                                        <path
                                            fill="none"
                                            stroke="#5c4033"
                                            stroke-width="3"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M14 27l8 8 16-16"
                                            class="check"/>


                                    </svg>


                                </div>


                                <h4
                                    class="fw-bold mb-1 titulo-exito">


                                    ¡Pedido Registrado!


                                </h4>


                                <div
                                    class="badge pedido-badge fs-6 px-4 py-2 mb-3">


                                    📦 Pedido #${numeroPedido}


                                </div>


                                <p class="text-muted mb-1">


                                    Tu pedido ha sido recibido correctamente.


                                </p>


                                <p class="text-muted mb-1">


                                    <strong>${nombre}</strong>,
                                    gracias por tu compra.


                                </p>


                                <p class="text-muted mb-4">


                                    En un momento te redirigimos
                                    a WhatsApp para confirmar
                                    con el restaurante.


                                </p>


                                <div
                                    class="progress mb-3 barra-contenedor">


                                    <div
                                        id="barraProgreso"
                                        class="progress-bar barra-progreso"
                                        role="progressbar"
                                        style="width:0%;">


                                    </div>


                                </div>


                                <p
                                    class="text-muted texto-redireccion">


                                    Redirigiendo a WhatsApp en


                                    <strong>


                                        <span id="cuentaRegresiva">

                                            5

                                        </span>


                                    </strong>


                                    segundos...


                                </p>


                            </div>


                        </div>


                    </div>


                </div>

            `;


            document.body.appendChild(
                modalExito
            );


            let exito =
                new bootstrap.Modal(
                    document.getElementById(
                        'exitoModal'
                    )
                );


            exito.show();


            let progreso = 0;


            let cuenta = 5;


            let intervalo =
                setInterval(() => {


                    progreso +=
                        (100 / 50);


                    document.getElementById(
                        'barraProgreso'
                    ).style.width =
                        progreso + '%';


                    if(
                        progreso >= 20 &&
                        cuenta === 5
                    ){


                        cuenta = 4;


                        document.getElementById(
                            'cuentaRegresiva'
                        ).innerText =
                            cuenta;


                    }


                    if(
                        progreso >= 40 &&
                        cuenta === 4
                    ){


                        cuenta = 3;


                        document.getElementById(
                            'cuentaRegresiva'
                        ).innerText =
                            cuenta;


                    }


                    if(
                        progreso >= 60 &&
                        cuenta === 3
                    ){


                        cuenta = 2;


                        document.getElementById(
                            'cuentaRegresiva'
                        ).innerText =
                            cuenta;


                    }


                    if(
                        progreso >= 80 &&
                        cuenta === 2
                    ){


                        cuenta = 1;


                        document.getElementById(
                            'cuentaRegresiva'
                        ).innerText =
                            cuenta;


                    }


                    if(progreso >= 100){


                        clearInterval(
                            intervalo
                        );


                        exito.hide();


                        document.getElementById(
                            'exitoModal'
                        ).remove();


                        let numero =
                            "573103456584";


                        let urlWhatsApp =
                            `https://wa.me/${numero}?text=${mensajeCodificado}`;


                        let link =
                            document.createElement(
                                "a"
                            );


                        link.href =
                            urlWhatsApp;


                        link.target =
                            "_blank";


                        document.body.appendChild(
                            link
                        );


                        link.click();


                        link.remove();


                        carrito = [];


                        actualizarCarrito();


                    }


                },100);


        }else{


            alert(
                "❌ " + data.mensaje
            );


        }


    })


    .catch(err => {


        console.error(err);


        alert(
            "Error en el envío"
        );


    });

}


/* ============================================================
   FILTRO CATEGORÍAS
============================================================ */

document
    .querySelectorAll(".filtro-btn")
    .forEach(btn => {


        btn.addEventListener(
            "click",
            function(){


                document
                    .querySelectorAll(
                        ".filtro-btn"
                    )
                    .forEach(b =>
                        b.classList.remove(
                            "active"
                        )
                    );


                this.classList.add(
                    "active"
                );


                let categoria =
                    this.getAttribute(
                        "data-categoria"
                    );


                document
                    .querySelectorAll(
                        ".producto-item"
                    )
                    .forEach(item => {


                        if(
                            categoria === "all" ||
                            item.getAttribute(
                                "data-categoria"
                            ) === categoria
                        ){


                            item.style.display =
                                "block";


                        }else{


                            item.style.display =
                                "none";


                        }


                    });


                let offcanvas =
                    bootstrap.Offcanvas
                        .getInstance(
                            document.getElementById(
                                'menuCategorias'
                            )
                        );


                if(offcanvas){


                    offcanvas.hide();


                }


            }
        );


    });


</script>

<!-- ============================================================
     CSS — CAFÉ MONCA
============================================================ -->

<style>


/* ============================================================
   GENERAL
============================================================ */

body{
    background:#faf9f7 !important;
    color:#302a26;
    font-family:'Poppins',sans-serif;
}


/* ============================================================
   ENCABEZADO
============================================================ */

.encabezado-menu{
    padding:5px 3px 14px;
    border-bottom:1px solid #ebe7e3;
}


.titulo-menu{
    font-family:'Poppins',sans-serif;
    font-size:18px !important;
    font-weight:500 !important;
    color:#3f3028;
    letter-spacing:-.2px;
}


.btn-menu{
    width:40px;
    height:40px;
    padding:0;
    border:none;
    border-radius:10px;
    background:#3f3028 !important;
    font-size:17px;
}


.espacio-header{
    width:40px;
}


/* ============================================================
   ESTADO
============================================================ */

.estado-restaurante{
    border:none !important;
    border-radius:10px !important;
    padding:10px 14px !important;
    box-shadow:none !important;
    font-size:12px;
}


.estado-restaurante strong{
    font-weight:500;
}


.estado-restaurante small{
    font-size:10px;
}


/* ============================================================
   CATEGORÍAS
============================================================ */

.categorias-menu{
    display:flex;
    justify-content:center;
    align-items:center;
    flex-wrap:wrap;
    gap:6px;
}


.categorias-menu .filtro-btn{
    margin:0 !important;
    padding:7px 14px !important;
    border-radius:20px !important;
    border:1px solid #ddd6d0 !important;
    background:#fff !important;
    color:#66564c !important;
    font-family:'Poppins',sans-serif;
    font-size:11px !important;
    font-weight:400;
    transition:all .2s ease;
}


.categorias-menu .filtro-btn:hover{
    border-color:#5c4033 !important;
    color:#5c4033 !important;
}


.categorias-menu .filtro-btn.active{
    background:#5c4033 !important;
    border-color:#5c4033 !important;
    color:#fff !important;
}


/* ============================================================
   TARJETA
============================================================ */

.producto-item{
    padding-left:9px;
    padding-right:9px;
}


.producto-card{
    position:relative;
    background:#fff;
    border:1px solid #e9e4df !important;
    border-radius:14px !important;
    overflow:hidden;
    box-shadow:0 3px 12px rgba(55,40,30,.055);
    transition:
        transform .25s ease,
        box-shadow .25s ease;
}


.producto-card:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 24px rgba(55,40,30,.10);
}


/* ============================================================
   IMAGEN
============================================================ */

.img-container{
    position:relative;
    width:100%;
    height:235px;
    overflow:hidden;
    background:#eee9e5;
}


.img-container img{
    width:100%;
    height:100%;
    display:block;
    object-fit:cover;
    object-position:center;
    transition:transform .45s ease;
}


.producto-card:hover .img-container img{
    transform:scale(1.035);
}


.producto-card:hover .img-container img{
    transform:scale(1.035);
}


/* ============================================================
   INFORMACIÓN — DEBAJO DE LA FOTO
============================================================ */

.info-producto{
    position:relative;
    background:#fff;
    padding:13px 15px 15px;
    min-height:105px;
}


/* ============================================================
   NOMBRE
============================================================ */

.nombre-producto{
    margin:0 48px 5px 0 !important;
    font-family:'Poppins',sans-serif;
    font-size:15px !important;
    font-weight:600 !important;
    line-height:1.3;
    color:#3c3029 !important;
}


/* ============================================================
   DESCRIPCIÓN
============================================================ */

.descripcion-producto{
    margin:0 0 8px 0 !important;
    font-family:'Poppins',sans-serif;
    font-size:10.5px !important;
    font-weight:300;
    line-height:1.5;
    color:#89817b !important;

    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
}


/* ============================================================
   PRECIO
============================================================ */

.precio-producto{
    font-family:'Poppins',sans-serif;
    font-size:15px;
    font-weight:600;
    color:#5c4033;
}


/* ============================================================
   BOTÓN +
============================================================ */

.btn-add{
    position:absolute;
    right:12px;
    bottom:12px;

    width:38px;
    height:38px;

    padding:0 !important;

    border:0 !important;
    border-radius:50% !important;

    background:#5c4033 !important;
    color:#fff !important;

    font-size:21px;
    font-weight:300;

    display:flex;
    align-items:center;
    justify-content:center;

    box-shadow:0 4px 10px rgba(0,0,0,.16);

    z-index:10;

    transition:all .2s ease;
}


.btn-add:hover{
    background:#3f2b22 !important;
    transform:scale(1.06);
}


.btn-add:active{
    transform:scale(.94);
}


/* ============================================================
   CERRADO
============================================================ */

.btn-cerrado{
    border:none !important;
    border-radius:0 !important;
    background:#eeeae7 !important;
    color:#806f66 !important;
    font-size:11px;
    padding:9px;
}


/* MODAL DETALLE DE PRODUCTO */
.producto-detalle-dialog{max-width:460px;margin:1rem auto;}
.producto-detalle-modal{overflow:hidden;border-radius:18px !important;background:#fff;}
.producto-detalle-imagen-wrap{position:relative;width:100%;height:260px;background:#eee9e5;}
.producto-detalle-imagen{display:block;width:100%;height:100%;object-fit:cover;}
.producto-detalle-cerrar{position:absolute;top:12px;right:12px;background-color:#fff;border-radius:50%;padding:9px;opacity:.95;}
.producto-detalle-cuerpo{padding:20px;}
.producto-detalle-titulo{margin:0 0 10px;color:#3f3028;font-size:21px;font-weight:600;}
.producto-detalle-descripcion{color:#766b65;font-size:13px;line-height:1.6;white-space:pre-line;}
.producto-detalle-pie{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:18px;}
.producto-detalle-precio{color:#5c4033;font-size:19px;white-space:nowrap;}
.producto-detalle-agregar{border:0;border-radius:9px;padding:10px 15px;background:#5c4033;color:#fff;font-size:12px;}
.producto-detalle-agregar:hover{background:#3f2b22;color:#fff;}
.producto-detalle-agregar:disabled{background:#e8e2de;color:#806f66;}

/* ============================================================
   CARRITO
============================================================ */

.btn-carrito{
    position:fixed;
    right:20px;
    bottom:80px;

    width:54px;
    height:54px;

    padding:0 !important;

    display:flex;
    align-items:center;
    justify-content:center;

    background:#5c4033 !important;
    color:#fff !important;

    border:none !important;
    border-radius:50% !important;

    font-size:19px;

    box-shadow:0 5px 18px rgba(0,0,0,.20);

    z-index:999;

    transition:all .2s ease;
}


.btn-carrito:hover{
    background:#3f2b22 !important;
    transform:translateY(-2px);
}


/* ============================================================
   CONTADOR
============================================================ */

#contador{
    position:absolute;
    top:-4px;
    right:-3px;

    min-width:20px;
    height:20px;

    padding:0 5px !important;

    display:flex;
    align-items:center;
    justify-content:center;

    background:#a84d48 !important;
    color:#fff;

    border:2px solid #fff;

    border-radius:50%;

    font-size:10px;
    font-weight:600;
}


/* ============================================================
   OFFCANVAS CATEGORÍAS
============================================================ */

#menuCategorias{
    border:none;
    border-radius:0 18px 18px 0;
}


#menuCategorias .offcanvas-header{
    padding:20px;
    border-bottom:1px solid #e9e4df;
}


#menuCategorias .offcanvas-header h5{
    margin:0;
    font-size:16px;
    font-weight:500;
    color:#3f3028;
}


#menuCategorias .offcanvas-body{
    padding:18px;
    background:#faf9f7;
}


#menuCategorias .filtro-btn{
    border:1px solid #ddd6d0 !important;
    background:#fff !important;
    color:#66564c !important;
    border-radius:9px !important;
    font-size:12px;
    padding:10px;
    text-align:left;
}


#menuCategorias .filtro-btn.active{
    background:#5c4033 !important;
    color:#fff !important;
    border-color:#5c4033 !important;
}


/* ============================================================
   OFFCANVAS CARRITO
============================================================ */

#carritoCanvas{
    border:none;
    border-radius:18px 0 0 18px;
}


#carritoCanvas .offcanvas-header{
    padding:18px 20px;
    border-bottom:1px solid #e9e4df;
}


#carritoCanvas .offcanvas-header h5{
    margin:0;
    font-size:16px;
    font-weight:500;
    color:#3f3028;
}


#listaCarrito .list-group-item{
    border:1px solid #e9e4df;
    margin-bottom:8px;
    border-radius:10px !important;
    background:#fff;
    font-size:12px;
}


#listaCarrito strong{
    font-weight:500;
}


#carritoCanvas .btn-success{
    background:#5c4033 !important;
    border-color:#5c4033 !important;
    border-radius:9px;
    font-size:12px;
}


#carritoCanvas .btn-success:hover{
    background:#3f2b22 !important;
}


/* ============================================================
   MODALES
============================================================ */

.modal-content{
    border:none !important;
    border-radius:16px !important;
    box-shadow:0 15px 45px rgba(0,0,0,.15);
}


.modal-header{
    border-bottom:1px solid #e9e4df;
    padding:17px 20px;
}


.modal-title{
    font-size:15px;
    font-weight:500;
    color:#3f3028;
}


.modal-body{
    padding:20px;
}


.modal-footer{
    border-top:1px solid #e9e4df;
    padding:14px 20px;
}


.modal input{
    border:1px solid #ddd7d2;
    border-radius:9px;
    font-size:12px;
    padding:10px 12px;
}


.modal input:focus{
    border-color:#5c4033;
    box-shadow:0 0 0 3px rgba(92,64,51,.08);
}


.modal .btn-success{
    background:#5c4033 !important;
    border-color:#5c4033 !important;
    border-radius:9px;
}


/* ============================================================
   ALERTA PERSONALIZADA
============================================================ */

.modal-alerta-custom{
    display:none;
    position:fixed;

    top:0;
    left:0;

    width:100%;
    height:100%;

    background:rgba(45,35,30,.38);

    backdrop-filter:blur(3px);

    z-index:9999;

    justify-content:center;
    align-items:center;
}


.contenido-alerta{
    background:#fff;

    padding:25px;

    border-radius:16px;

    width:300px;

    text-align:center;

    border:1px solid #e9e4df;

    box-shadow:0 15px 40px rgba(0,0,0,.16);
}


.contenido-alerta h4{
    margin-bottom:10px;
    font-size:17px;
    color:#3f3028;
}


.contenido-alerta p{
    font-size:13px;
    color:#777;
}


.contenido-alerta button{
    margin-top:15px;
    padding:8px 18px;
    border:none;
    background:#5c4033;
    color:#fff;
    border-radius:8px;
    font-size:12px;
}


/* ============================================================
   MODAL ÉXITO
============================================================ */

.modal-exito{
    border-radius:18px !important;
}


.titulo-exito{
    color:#5c4033 !important;
}


.pedido-badge{
    background:#5c4033 !important;
    border-radius:20px;
    font-weight:400;
}


.barra-contenedor{
    height:7px;
    border-radius:10px;
    background:#eee9e5;
}


.barra-progreso{
    background:#5c4033 !important;
    transition:width .1s linear;
}


.texto-redireccion{
    font-size:12px;
}


/* ============================================================
   ANIMACIÓN ÉXITO
============================================================ */

@keyframes trazarCirculo{

    to{
        stroke-dashoffset:0;
    }

}


@keyframes trazarCheck{

    to{
        stroke-dashoffset:0;
    }

}


.circulo{
    stroke-dasharray:166;
    stroke-dashoffset:166;
    animation:
        trazarCirculo .6s ease forwards;
}


.check{
    stroke-dasharray:48;
    stroke-dashoffset:48;
    animation:
        trazarCheck .4s .6s ease forwards;
}


/* ============================================================
   NOTIFICACIÓN
============================================================ */

.notificacion-carrito{
    position:fixed;

    bottom:140px;
    right:20px;

    background:#5c4033;

    color:#fff;

    padding:11px 16px;

    border-radius:10px;

    font-size:12px;

    z-index:9999;

    box-shadow:0 5px 18px rgba(0,0,0,.18);

    animation:
        aparecerNotificacion .3s ease;
}


@keyframes aparecerNotificacion{

    from{
        opacity:0;
        transform:translateY(10px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }

}


/* ============================================================
   POPUP PROMOCIÓN
============================================================ */

.promocion-dialog{

    display:flex !important;

    flex-direction:column;

    width:auto !important;

    max-width:calc(100vw - 30px) !important;

    margin:1rem auto !important;

}


.promocion-modal{

    display:inline-block !important;

    width:auto !important;

    max-width:100% !important;

    min-width:0 !important;

    margin:0 !important;

    padding:0 !important;

    overflow:hidden;

    border:none !important;

    border-radius:18px !important;

    background:#fff;

    box-shadow:
        0 20px 60px rgba(0,0,0,.25);

}


.promocion-imagen-container{

    display:block;

    width:auto;

    max-width:100%;

    height:auto;

    max-height:none;

    overflow:hidden;

    margin:0;

    padding:0;

    background:#f5f2ef;

}


.promocion-imagen{

    display:block;

    width:auto !important;

    height:auto !important;

    max-width:100% !important;

    max-height:75vh !important;

    object-fit:contain;

    object-position:center;

    margin:0 auto;

}


.promocion-contenido{

    position:relative;

    width:100%;

    box-sizing:border-box;

    padding:20px 20px 17px;

    text-align:center;

}


.promocion-cerrar{

    position:absolute;

    top:10px;

    right:10px;

    width:28px;

    height:28px;

    padding:0;

    background-color:rgba(255,255,255,.95);

    border-radius:50%;

    opacity:.85;

    box-shadow:
        0 2px 8px rgba(0,0,0,.12);

    z-index:5;

}


.promocion-cerrar:hover{

    opacity:1;

}


.promocion-etiqueta{

    display:inline-block;

    margin-bottom:7px;

    padding:5px 10px;

    border-radius:20px;

    background:#f1ebe7;

    color:#5c4033;

    font-family:'Poppins',sans-serif;

    font-size:8px;

    font-weight:600;

    letter-spacing:1px;

}


.promocion-titulo{

    margin:0 25px 7px;

    color:#3f3028;

    font-family:'Poppins',sans-serif;

    font-size:20px;

    font-weight:600;

    line-height:1.3;

}


.promocion-descripcion{

    width:100%;

    max-width:none;

    margin:0 auto 14px;

    color:#766b65;

    font-family:'Poppins',sans-serif;

    font-size:11px;

    font-weight:300;

    line-height:1.55;

}


.promocion-boton{

    display:inline-block;

    padding:8px 20px;

    border:none !important;

    border-radius:9px !important;

    background:#5c4033 !important;

    color:#fff !important;

    font-family:'Poppins',sans-serif;

    font-size:10px;

    font-weight:500;

    box-shadow:
        0 5px 15px rgba(92,64,51,.20);

    transition:all .2s ease;

}


.promocion-boton:hover{

    background:#3f2b22 !important;

    color:#fff !important;

    transform:translateY(-1px);

}


.promocion-vigencia{

    margin-top:10px;

}


.promocion-vigencia small{

    color:#a09791;

    font-family:'Poppins',sans-serif;

    font-size:8px;

}


/* ============================================================
   RESPONSIVE
============================================================ */

@media(max-width:767px){

    /* Dos productos por fila en celulares */
    .productos-grid{margin-left:-4px;margin-right:-4px;}
    .producto-item{padding-left:4px;padding-right:4px;}
    .img-container{height:125px;}
    .info-producto{padding:9px 9px 12px;min-height:100px;}
    .nombre-producto{margin-right:35px !important;font-size:12px !important;}
    .descripcion-producto{font-size:9px !important;-webkit-line-clamp:2;}
    .precio-producto{font-size:13px;}
    .btn-add{width:30px;height:30px;right:7px;bottom:8px;font-size:18px;}
    .producto-detalle-dialog{max-width:calc(100vw - 24px);margin:12px auto;}
    .producto-detalle-imagen-wrap{height:220px;}
    .producto-detalle-cuerpo{padding:16px;}
    .producto-detalle-titulo{font-size:18px;}
    .producto-detalle-pie{align-items:flex-start;flex-direction:column;}
    .producto-detalle-agregar{width:100%;}

    .contenedor-menu{

        padding-left:8px;

        padding-right:8px;

    }


    .titulo-menu{

        font-size:15px !important;

        text-align:center;

    }


    .img-container{

        height:205px;

    }


    .producto-item{

        padding-left:6px;

        padding-right:6px;

        margin-bottom:14px !important;

    }


    .info-producto{

        padding:11px 12px 14px;

        min-height:105px;

    }


    .nombre-producto{

        font-size:14px !important;

    }


    .descripcion-producto{

        font-size:10px !important;

    }


    .precio-producto{

        font-size:14px;

    }


    .btn-add{

        width:36px;

        height:36px;

        right:10px;

        bottom:10px;

    }


    .categorias-menu{

        justify-content:flex-start;

        flex-wrap:nowrap;

        overflow-x:auto;

        padding-bottom:5px;

        scrollbar-width:none;

    }


    .categorias-menu::-webkit-scrollbar{

        display:none;

    }


    .categorias-menu .filtro-btn{

        flex-shrink:0;

    }


    /* ========================================================
       POPUP EN CELULAR
    ======================================================== */

    .promocion-dialog{

        width:auto !important;

        max-width:calc(100vw - 20px) !important;

        margin:1rem auto !important;

    }


    .promocion-modal{

        width:auto !important;

        max-width:100% !important;

        min-width:0 !important;

    }


    .promocion-imagen-container{

        width:auto;

        max-width:100%;

        max-height:none;

    }


    .promocion-imagen{

        width:auto !important;

        height:auto !important;

        max-width:100% !important;

        max-height:65vh !important;

    }


    .promocion-contenido{

        width:100%;

        padding:17px 15px 15px;

    }


    .promocion-titulo{

        font-size:18px;

        margin-left:15px;

        margin-right:15px;

    }


    .promocion-descripcion{

        font-size:10px;

        line-height:1.5;

    }


    .promocion-boton{

        min-width:130px;

        padding:8px 16px;

    }


    .btn-carrito{

        right:15px;

        bottom:70px;

        width:52px;

        height:52px;

    }


    .notificacion-carrito{

        right:15px;

        bottom:135px;

        max-width:calc(100% - 30px);

    }

}


/* ============================================================
   ESCRITORIO
============================================================ */

@media(min-width:992px){

    .producto-item{

        padding-left:10px;

        padding-right:10px;

    }


    .img-container{

        height:245px;

    }


    .promocion-dialog{

        width:auto !important;

        max-width:calc(100vw - 40px) !important;

    }


    .promocion-modal{

        width:auto !important;

        max-width:100% !important;

    }


    .promocion-imagen{

        width:auto !important;

        height:auto !important;

        max-width:100% !important;

        max-height:75vh !important;

    }

}

</style>

<?php require_once __DIR__ . '/../layouts/footer_cliente.php'; ?>