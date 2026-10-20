            const fechaHoraComoTexto = (valor) => {
                const partes = String(valor || '').match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}:\d{2})/);
                return partes ? `${partes[1]}/${partes[2]}/${partes[3]} ${partes[4]}` : '';
            };
            const textoComoFechaHora = (texto) => {
                const partes = String(texto || '').trim().match(/^(\d{4})\/(\d{2})\/(\d{2})\s+(\d{1,2}):(\d{2})$/);
                if (!partes) return '';
                const hora = Number(partes[4]);
                const minuto = Number(partes[5]);
                const fecha = new Date(Number(partes[1]), Number(partes[2]) - 1, Number(partes[3]), hora, minuto);
                if (fecha.getFullYear() !== Number(partes[1]) || fecha.getMonth() !== Number(partes[2]) - 1 || fecha.getDate() !== Number(partes[3]) || fecha.getHours() !== hora || fecha.getMinutes() !== minuto || hora > 23 || minuto > 59) return '';
                return `${partes[1]}-${partes[2]}-${partes[3]}T${String(hora).padStart(2, '0')}:${partes[5]}`;
            };

            // 2026-10-20: calendario compacto alineado encima del campo, como el diseño de referencia.
            const crearMiniCalendarioFecha = (texto, nativo) => {
                let mesVisible = new Date();
                const popover = document.createElement('div');
                popover.className = 'fecha-mini-calendario';
                popover.setAttribute('role', 'dialog');
                popover.setAttribute('aria-label', 'Seleccionar fecha');
                document.body.appendChild(popover);
                const claveFecha = (fecha) => `${fecha.getFullYear()}-${String(fecha.getMonth() + 1).padStart(2, '0')}-${String(fecha.getDate()).padStart(2, '0')}`;
                const fechaSeleccionada = () => (nativo.value || textoComoFechaHora(texto.value)).slice(0, 10);
                const dibujar = () => {
                    const anio = mesVisible.getFullYear();
                    const mes = mesVisible.getMonth();
                    const tituloMes = new Intl.DateTimeFormat('es-BO', { month: 'long', year: 'numeric' }).format(new Date(anio, mes, 1));
                    const seleccion = fechaSeleccionada();
                    const hoy = claveFecha(new Date());
                    const inicio = new Date(anio, mes, 1).getDay();
                    const diasMes = new Date(anio, mes + 1, 0).getDate();
                    const cantidad = Math.ceil((inicio + diasMes) / 7) * 7;
                    popover.innerHTML = `<div class="fecha-mini-cal-header"><button type="button" class="fecha-mini-nav" data-mes="-1" aria-label="Mes anterior">‹</button><span class="fecha-mini-mes">${tituloMes}</span><button type="button" class="fecha-mini-nav" data-mes="1" aria-label="Mes siguiente">›</button></div><div class="fecha-mini-grid">${['DO','LU','MA','MI','JU','VI','SA'].map(d => `<span class="fecha-mini-semana">${d}</span>`).join('')}</div><div class="fecha-mini-grid fecha-mini-dias"></div>`;
                    const dias = popover.querySelector('.fecha-mini-dias');
                    for (let indice = 0; indice < cantidad; indice++) {
                        const fecha = new Date(anio, mes, indice - inicio + 1);
                        const boton = document.createElement('button');
                        boton.type = 'button';
                        boton.className = 'fecha-mini-dia';
                        boton.textContent = fecha.getDate();
                        boton.dataset.fecha = claveFecha(fecha);
                        if (fecha.getMonth() !== mes) boton.classList.add('otro-mes');
                        if (boton.dataset.fecha === hoy) boton.classList.add('hoy');
                        if (boton.dataset.fecha === seleccion) boton.classList.add('seleccionado');
                        dias.appendChild(boton);
                    }
                };
                const posicionar = () => {
                    const rect = texto.getBoundingClientRect();
                    popover.style.visibility = 'hidden';
                    popover.classList.add('is-open');
                    const ancho = popover.offsetWidth;
                    const alto = popover.offsetHeight;
                    const izquierda = Math.max(8, Math.min(rect.left, window.innerWidth - ancho - 8));
                    let arriba = rect.top - alto - 4;
                    if (arriba < 8) arriba = Math.min(window.innerHeight - alto - 8, rect.bottom + 4);
                    popover.style.left = izquierda + 'px';
                    popover.style.top = Math.max(8, arriba) + 'px';
                    popover.style.visibility = '';
                };
                const abrir = () => {
                    const valor = fechaSeleccionada();
                    const base = valor ? new Date(valor + 'T12:00:00') : new Date();
                    mesVisible = new Date(base.getFullYear(), base.getMonth(), 1);
                    dibujar();
                    posicionar();
                };
                const cerrar = () => popover.classList.remove('is-open');
                const actualizar = () => { if (popover.classList.contains('is-open')) { dibujar(); posicionar(); } };
                popover.addEventListener('click', (evento) => {
                    evento.stopPropagation();
                    const navegacion = evento.target.closest('[data-mes]');
                    if (navegacion) {
                        mesVisible.setMonth(mesVisible.getMonth() + Number(navegacion.dataset.mes));
                        dibujar();
                        posicionar();
                        return;
                    }
                    const dia = evento.target.closest('[data-fecha]');
                    if (!dia) return;
                    const hora = nativo.value ? nativo.value.slice(11, 16) : '08:30';
                    nativo.value = `${dia.dataset.fecha}T${hora}`;
                    texto.value = fechaHoraComoTexto(nativo.value);
                    texto.setCustomValidity('');
                    cerrar();
                    texto.focus();
                });
                document.addEventListener('click', (evento) => {
                    if (evento.target !== texto && !popover.contains(evento.target)) cerrar();
                });
                window.addEventListener('scroll', cerrar, true);
                window.addEventListener('resize', cerrar);
                return { abrir, actualizar };
            };


import assert from 'node:assert/strict';
assert.equal(textoComoFechaHora('2026/10/24 8:33'), '2026-10-24T08:33');
assert.equal(textoComoFechaHora('2026/02/30 8:33'), '');
assert.equal(textoComoFechaHora('2026/10/24 25:33'), '');
assert.equal(textoComoFechaHora('2026/10/24 8:60'), '');
console.log('Supervisar: fecha y hora válida, y rechazos de fecha/hora incorrecta OK');

