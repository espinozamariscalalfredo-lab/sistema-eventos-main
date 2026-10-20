            function formatoFechaHora(valor) {
                var coincidencia = String(valor || '').match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}:\d{2})/);
                return coincidencia ? `${coincidencia[1]}/${coincidencia[2]}/${coincidencia[3]} ${coincidencia[4]}` : '';
            }

            function valorFechaHora(texto) {
                var coincidencia = String(texto || '').trim().match(/^(\d{4})\/(\d{2})\/(\d{2})\s+(\d{1,2}):(\d{2})$/);
                if (!coincidencia) return '';

                var hora = Number(coincidencia[4]);
                var minuto = Number(coincidencia[5]);
                var fecha = new Date(Number(coincidencia[1]), Number(coincidencia[2]) - 1, Number(coincidencia[3]), hora, minuto);
                if (fecha.getFullYear() !== Number(coincidencia[1]) || fecha.getMonth() !== Number(coincidencia[2]) - 1 || fecha.getDate() !== Number(coincidencia[3]) || fecha.getHours() !== hora || fecha.getMinutes() !== minuto || hora > 23 || minuto > 59) return '';

                return `${coincidencia[1]}-${coincidencia[2]}-${coincidencia[3]}T${String(hora).padStart(2, '0')}:${coincidencia[5]}`;
            }

            // 2026-10-20: calendario compacto de 194 px, con semana de domingo a sábado.
            function crearMiniCalendarioFecha(contenedor, texto, nativo) {
                var mesVisible = new Date();
                var popover = document.createElement('div');
                popover.className = 'fecha-mini-calendario';
                popover.setAttribute('role', 'dialog');
                popover.setAttribute('aria-label', 'Seleccionar fecha');
                document.body.appendChild(popover);

                function claveFecha(fecha) {
                    return `${fecha.getFullYear()}-${String(fecha.getMonth() + 1).padStart(2, '0')}-${String(fecha.getDate()).padStart(2, '0')}`;
                }
                function fechaSeleccionada() {
                    var valor = nativo.value || valorFechaHora(texto.value);
                    return valor ? valor.slice(0, 10) : '';
                }
                function dibujar() {
                    var anio = mesVisible.getFullYear();
                    var mes = mesVisible.getMonth();
                    var tituloMes = new Intl.DateTimeFormat('es-BO', { month: 'long', year: 'numeric' }).format(new Date(anio, mes, 1));
                    var seleccion = fechaSeleccionada();
                    var hoy = claveFecha(new Date());
                    var inicio = new Date(anio, mes, 1).getDay();
                    var diasMes = new Date(anio, mes + 1, 0).getDate();
                    var cantidad = Math.ceil((inicio + diasMes) / 7) * 7;

                    popover.innerHTML = `<div class="fecha-mini-cal-header"><button type="button" class="fecha-mini-nav" data-mes="-1" aria-label="Mes anterior">‹</button><span class="fecha-mini-mes">${tituloMes}</span><button type="button" class="fecha-mini-nav" data-mes="1" aria-label="Mes siguiente">›</button></div><div class="fecha-mini-grid">${['DO','LU','MA','MI','JU','VI','SA'].map(d => `<span class="fecha-mini-semana">${d}</span>`).join('')}</div><div class="fecha-mini-grid fecha-mini-dias"></div>`;
                    var dias = popover.querySelector('.fecha-mini-dias');
                    for (var indice = 0; indice < cantidad; indice++) {
                        var fecha = new Date(anio, mes, indice - inicio + 1);
                        var boton = document.createElement('button');
                        boton.type = 'button';
                        boton.className = 'fecha-mini-dia';
                        boton.textContent = fecha.getDate();
                        boton.dataset.fecha = claveFecha(fecha);
                        if (fecha.getMonth() !== mes) boton.classList.add('otro-mes');
                        if (boton.dataset.fecha === hoy) boton.classList.add('hoy');
                        if (boton.dataset.fecha === seleccion) boton.classList.add('seleccionado');
                        dias.appendChild(boton);
                    }
                }
                function posicionar() {
                    var rect = texto.getBoundingClientRect();
                    popover.style.visibility = 'hidden';
                    popover.classList.add('is-open');
                    var ancho = popover.offsetWidth;
                    var alto = popover.offsetHeight;
                    var izquierda = Math.max(8, Math.min(rect.left, window.innerWidth - ancho - 8));
                    var arriba = rect.top - alto - 4;
                    if (arriba < 8) arriba = Math.min(window.innerHeight - alto - 8, rect.bottom + 4);
                    popover.style.left = izquierda + 'px';
                    popover.style.top = Math.max(8, arriba) + 'px';
                    popover.style.visibility = '';
                }
                function abrir() {
                    var valor = fechaSeleccionada();
                    var base = valor ? new Date(valor + 'T12:00:00') : new Date();
                    mesVisible = new Date(base.getFullYear(), base.getMonth(), 1);
                    dibujar();
                    posicionar();
                }
                function cerrar() { popover.classList.remove('is-open'); }
                function actualizar() { if (popover.classList.contains('is-open')) { dibujar(); posicionar(); } }

                popover.addEventListener('click', function(evento) {
                    evento.stopPropagation();
                    var navegacion = evento.target.closest('[data-mes]');
                    if (navegacion) {
                        mesVisible.setMonth(mesVisible.getMonth() + Number(navegacion.dataset.mes));
                        dibujar();
                        posicionar();
                        return;
                    }
                    var dia = evento.target.closest('[data-fecha]');
                    if (!dia) return;
                    var horaActual = nativo.value ? nativo.value.slice(11, 16) : '08:30';
                    nativo.value = dia.dataset.fecha + 'T' + horaActual;
                    texto.value = formatoFechaHora(nativo.value);
                    texto.setCustomValidity('');
                    cerrar();
                    texto.focus();
                });
                document.addEventListener('click', function(evento) {
                    if (evento.target !== texto && !popover.contains(evento.target)) cerrar();
                });
                window.addEventListener('scroll', cerrar, true);
                window.addEventListener('resize', cerrar);
                return { abrir: abrir, cerrar: cerrar, actualizar: actualizar };
            }


import assert from 'node:assert/strict';
assert.equal(valorFechaHora('2026/10/24 8:33'), '2026-10-24T08:33');
assert.equal(valorFechaHora('2026/02/30 8:33'), '');
assert.equal(valorFechaHora('2026/10/24 25:33'), '');
assert.equal(valorFechaHora('2026/10/24 8:60'), '');
console.log('Usar: fecha y hora válida, y rechazos de fecha/hora incorrecta OK');

