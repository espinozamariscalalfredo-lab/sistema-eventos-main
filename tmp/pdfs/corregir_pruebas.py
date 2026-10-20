from pathlib import Path
for nombre in ('usar-fecha.js','supervisar-fecha.js'):
    ruta=Path('tmp/pdfs')/nombre
    texto=ruta.read_text(encoding='utf-8').replace('\\n','\n')
    ruta.write_text(texto,encoding='utf-8')
