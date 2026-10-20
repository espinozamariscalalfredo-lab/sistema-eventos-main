from pathlib import Path
u=Path('resources/views/salones/usar.blade.php').read_text(encoding='utf-8')
s=Path('resources/views/salones/supervisar.blade.php').read_text(encoding='utf-8')
a=u.index('            function formatoFechaHora(valor)')
b=u.index('            function prepararCamposFechaHora()',a)
Path('tmp/pdfs/usar-fecha.js').write_text(u[a:b],encoding='utf-8')
a=s.index('            const fechaHoraComoTexto =')
b=s.index("            document.querySelectorAll('input[type=\"datetime-local\"]')",a)
Path('tmp/pdfs/supervisar-fecha.js').write_text(s[a:b],encoding='utf-8')
