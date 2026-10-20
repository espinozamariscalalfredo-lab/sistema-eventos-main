from pathlib import Path
from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import mm
from reportlab.platypus import (
    BaseDocTemplate, PageTemplate, Frame, Paragraph, Spacer, PageBreak,
    Table, TableStyle, Flowable, KeepTogether, HRFlowable
)

ROOT = Path(__file__).resolve().parents[2]
OUT = ROOT / "output" / "pdf" / "guia_visual_para_principiantes_sistema_reservas.pdf"
OUT.parent.mkdir(parents=True, exist_ok=True)
PAGE_W, PAGE_H = A4
MARGIN_X, MARGIN_TOP, MARGIN_BOTTOM = 17*mm, 17*mm, 16*mm
CONTENT_W = PAGE_W - 2*MARGIN_X

NAVY = colors.HexColor("#17324D")
BLUE = colors.HexColor("#2563EB")
PALE_BLUE = colors.HexColor("#EFF6FF")
TEAL = colors.HexColor("#0F9D91")
GOLD = colors.HexColor("#E9A900")
INK = colors.HexColor("#243449")
MUTED = colors.HexColor("#63748B")
LINE = colors.HexColor("#D9E2EC")
PALE = colors.HexColor("#F5F8FC")
GREEN = colors.HexColor("#16834A")
RED = colors.HexColor("#B42318")

styles = getSampleStyleSheet()
styles.add(ParagraphStyle(name="CoverSmall", parent=styles["Normal"], fontName="Helvetica-Bold", fontSize=9, leading=13, textColor=colors.white, alignment=TA_LEFT, spaceAfter=8))
styles.add(ParagraphStyle(name="CoverTitle", parent=styles["Title"], fontName="Helvetica-Bold", fontSize=27, leading=32, textColor=colors.white, alignment=TA_LEFT, spaceAfter=10))
styles.add(ParagraphStyle(name="CoverSub", parent=styles["Normal"], fontName="Helvetica", fontSize=12, leading=18, textColor=colors.HexColor("#DFEAFE"), alignment=TA_LEFT))
styles.add(ParagraphStyle(name="H1N", parent=styles["Heading1"], fontName="Helvetica-Bold", fontSize=18, leading=22, textColor=NAVY, spaceBefore=0, spaceAfter=7))
styles.add(ParagraphStyle(name="H2N", parent=styles["Heading2"], fontName="Helvetica-Bold", fontSize=11.5, leading=14, textColor=BLUE, spaceBefore=7, spaceAfter=4))
styles.add(ParagraphStyle(name="BodyN", parent=styles["BodyText"], fontName="Helvetica", fontSize=9.2, leading=13.4, textColor=INK, spaceAfter=5))
styles.add(ParagraphStyle(name="SmallN", parent=styles["BodyText"], fontName="Helvetica", fontSize=8, leading=10.5, textColor=INK, spaceAfter=2))
styles.add(ParagraphStyle(name="TinyN", parent=styles["BodyText"], fontName="Helvetica", fontSize=7.1, leading=9.1, textColor=INK, spaceAfter=1))
styles.add(ParagraphStyle(name="TableHeadN", parent=styles["BodyText"], fontName="Helvetica-Bold", fontSize=7.7, leading=9.4, textColor=colors.white))
styles.add(ParagraphStyle(name="TableCellN", parent=styles["BodyText"], fontName="Helvetica", fontSize=7.5, leading=9.5, textColor=INK))
styles.add(ParagraphStyle(name="CodeN", parent=styles["Code"], fontName="Courier", fontSize=7.6, leading=10.2, textColor=colors.HexColor("#E2E8F0"), backColor=NAVY, borderColor=NAVY, borderWidth=5, borderPadding=8, leftIndent=3, rightIndent=3, spaceBefore=3, spaceAfter=6))
styles.add(ParagraphStyle(name="CallN", parent=styles["BodyText"], fontName="Helvetica", fontSize=8.5, leading=11.5, textColor=INK))
styles.add(ParagraphStyle(name="CenterSmall", parent=styles["SmallN"], alignment=TA_CENTER))

def P(text, style="BodyN"):
    return Paragraph(text, styles[style])

def table(rows, widths, header=True, font="TableCellN", repeat=1):
    cooked=[]
    for ri,row in enumerate(rows):
        cooked.append([Paragraph(str(v), styles["TableHeadN"] if header and ri == 0 else styles[font]) for v in row])
    t=Table(cooked, colWidths=widths, repeatRows=repeat if header else 0, hAlign="LEFT")
    rules=[("VALIGN",(0,0),(-1,-1),"TOP"),("LEFTPADDING",(0,0),(-1,-1),5),("RIGHTPADDING",(0,0),(-1,-1),5),("TOPPADDING",(0,0),(-1,-1),4),("BOTTOMPADDING",(0,0),(-1,-1),4),("GRID",(0,0),(-1,-1),0.35,LINE)]
    if header: rules += [("BACKGROUND",(0,0),(-1,0),NAVY),("ROWBACKGROUNDS",(0,1),(-1,-1),[colors.white,PALE])]
    else: rules += [("ROWBACKGROUNDS",(0,0),(-1,-1),[colors.white,PALE])]
    t.setStyle(TableStyle(rules)); return t

def callout(title, body, tone="blue"):
    palette={"blue":(PALE_BLUE,BLUE),"green":(colors.HexColor("#ECFDF3"),GREEN),"gold":(colors.HexColor("#FFF8E5"),GOLD),"red":(colors.HexColor("#FEF3F2"),RED)}
    bg,accent=palette[tone]
    t=Table([[Paragraph(f"<b>{title}</b><br/>{body}",styles["CallN"])]],colWidths=[CONTENT_W])
    t.setStyle(TableStyle([("BACKGROUND",(0,0),(-1,-1),bg),("BOX",(0,0),(-1,-1),0.65,accent),("LINEBEFORE",(0,0),(0,0),3,accent),("LEFTPADDING",(0,0),(-1,-1),9),("RIGHTPADDING",(0,0),(-1,-1),8),("TOPPADDING",(0,0),(-1,-1),7),("BOTTOMPADDING",(0,0),(-1,-1),7)]))
    return t

def bullets(items, style="BodyN"):
    return [Paragraph("• " + item, styles[style], bulletText=None) for item in items]

class PathDiagram(Flowable):
    def __init__(self, boxes, width=CONTENT_W, height=107, vertical=False):
        super().__init__(); self.boxes=boxes; self.width=width; self.height=height; self.vertical=vertical
    def draw(self):
        c=self.canv
        if self.vertical:
            n=len(self.boxes); gap=8; bh=(self.height-(n-1)*gap)/n; bw=self.width*.88; x=(self.width-bw)/2
            coords=[]
            for i,(title,desc,col) in enumerate(self.boxes):
                y=self.height-(i+1)*bh-i*gap
                c.setFillColor(colors.white); c.setStrokeColor(col); c.setLineWidth(1); c.roundRect(x,y,bw,bh,6,fill=1,stroke=1)
                c.setFillColor(col); c.roundRect(x,y+bh-17,bw,17,6,fill=1,stroke=0); c.rect(x,y+bh-17,bw,6,fill=1,stroke=0)
                c.setFillColor(colors.white); c.setFont("Helvetica-Bold",8); c.drawCentredString(x+bw/2,y+bh-12,title)
                c.setFillColor(INK); c.setFont("Helvetica",7); c.drawCentredString(x+bw/2,y+6,desc)
                coords.append((x+bw/2,y))
            for i in range(n-1):
                x1,y1=coords[i]; x2,y2=coords[i+1]
                c.setStrokeColor(MUTED); c.setLineWidth(1); c.line(x1,y1-1,x2,y2+bh+1)
                c.line(x2-3,y2+bh+5,x2,y2+bh+1); c.line(x2+3,y2+bh+5,x2,y2+bh+1)
        else:
            n=len(self.boxes); gap=8; bw=(self.width-(n-1)*gap)/n; bh=self.height-20; y=10
            for i,(title,desc,col) in enumerate(self.boxes):
                x=i*(bw+gap)
                c.setFillColor(colors.white); c.setStrokeColor(col); c.setLineWidth(1); c.roundRect(x,y,bw,bh,6,fill=1,stroke=1)
                c.setFillColor(col); c.roundRect(x,y+bh-21,bw,21,6,fill=1,stroke=0); c.rect(x,y+bh-21,bw,7,fill=1,stroke=0)
                c.setFillColor(colors.white); c.setFont("Helvetica-Bold",7.3); c.drawCentredString(x+bw/2,y+bh-14,title)
                c.setFillColor(INK); c.setFont("Helvetica",6.8)
                lines=desc.split("|")
                for li,line in enumerate(lines): c.drawCentredString(x+bw/2,y+bh-38-li*10,line)
                if i<n-1:
                    ax=x+bw+1; ay=y+bh/2; c.setStrokeColor(MUTED); c.line(ax,ay,ax+gap-2,ay); c.line(ax+gap-5,ay+3,ax+gap-2,ay); c.line(ax+gap-5,ay-3,ax+gap-2,ay)

class GuideDoc(BaseDocTemplate):
    def __init__(self,path):
        super().__init__(path,pagesize=A4,title="Guía intuitiva para empezar con el sistema de reservas",author="Documentación del proyecto",leftMargin=MARGIN_X,rightMargin=MARGIN_X,topMargin=MARGIN_TOP,bottomMargin=MARGIN_BOTTOM)
        frame=Frame(MARGIN_X,MARGIN_BOTTOM,CONTENT_W,PAGE_H-MARGIN_TOP-MARGIN_BOTTOM,id="main",leftPadding=0,rightPadding=0,topPadding=0,bottomPadding=0)
        self.addPageTemplates([PageTemplate(id="all",frames=[frame],onPage=self.decorate)])
    def decorate(self,c,doc):
        if doc.page == 1: return
        c.saveState(); c.setStrokeColor(LINE); c.line(MARGIN_X,PAGE_H-10*mm,PAGE_W-MARGIN_X,PAGE_H-10*mm)
        c.setFont("Helvetica",7); c.setFillColor(MUTED)
        c.drawString(MARGIN_X,PAGE_H-8*mm,"GUÍA PARA EMPEZAR | SISTEMA DE RESERVAS")
        c.drawRightString(PAGE_W-MARGIN_X,9*mm,f"Visual Studio Code · Revisión 20/10/2026 · pág. {doc.page}")
        c.restoreState()

story=[]

# PORTADA
cover=Table([[Paragraph("GUÍA VISUAL PARA PRINCIPIANTES",styles["CoverSmall"])],[Paragraph("Aprende a recorrer<br/>el sistema paso a paso",styles["CoverTitle"])],[Paragraph("Una explicación sencilla para abrir el proyecto, entender sus partes y seguir una función desde la pantalla hasta la base de datos.",styles["CoverSub"])],[Spacer(1,8)],[Paragraph("Proyecto Laravel · vistas Blade · PostgreSQL",styles["CoverSmall"])],[Paragraph("Preparada para estudiar el código en Visual Studio Code<br/>20 de octubre de 2026",styles["CoverSub"]) ]],colWidths=[CONTENT_W-24])
cover.setStyle(TableStyle([("BACKGROUND",(0,0),(-1,-1),NAVY),("LEFTPADDING",(0,0),(-1,-1),17),("RIGHTPADDING",(0,0),(-1,-1),17),("TOPPADDING",(0,0),(-1,0),15),("BOTTOMPADDING",(0,0),(-1,0),4),("TOPPADDING",(0,1),(-1,1),3),("BOTTOMPADDING",(0,1),(-1,1),9),("TOPPADDING",(0,2),(-1,2),4),("BOTTOMPADDING",(0,2),(-1,2),8),("TOPPADDING",(0,3),(-1,3),4),("BOTTOMPADDING",(0,3),(-1,3),4),("TOPPADDING",(0,4),(-1,4),4),("BOTTOMPADDING",(0,4),(-1,4),4),("TOPPADDING",(0,5),(-1,5),4),("BOTTOMPADDING",(0,5),(-1,5),14)]))
story += [Spacer(1,28*mm),cover,Spacer(1,10*mm),callout("La idea principal", "No necesitas aprender todo de golpe. Primero identifica qué archivo hace cada trabajo. Después sigue una solicitud por el camino: pantalla → ruta → controlador → modelo → PostgreSQL.","blue"),Spacer(1,8),P("Este manual usa palabras comunes, ejemplos del proyecto y nombres de archivos reales. Los diagramas son mapas para estudiar, no pantallas nuevas del sistema.","BodyN"),PageBreak()]

# 1. MENTAL MODEL
story += [P("1 · Primero, entiende la idea", "H1N"),P("Piensa en el sistema como una oficina que recibe una petición, revisa si está permitida, consulta un archivador y devuelve una respuesta.")]
story.append(PathDiagram([
    ("NAVEGADOR","Lo que ve la persona",BLUE),
    ("RUTA","A qué puerta llegó",TEAL),
    ("CONTROLADOR","Reglas y decisiones",GOLD),
    ("MODELO","Lee y guarda datos",colors.HexColor("#7C3AED")),
    ("POSTGRESQL","Tablas del sistema",GREEN),
],height=100))
story += [P("El proyecto está unido en una sola aplicación Laravel. “Frontend” y “backend” son responsabilidades diferentes dentro del mismo proyecto; todavía no son dos aplicaciones separadas.","BodyN"),P("Palabras sin misterio", "H2N"),table([
    ["Palabra", "En palabras sencillas", "Un ejemplo aquí"],
    ["Frontend", "La parte que la persona ve y usa.", "El calendario y sus botones."],
    ["Backend", "La parte que recibe datos y aplica reglas.", "Revisar permisos antes de guardar."],
    ["Base de datos", "Un archivador ordenado en tablas.", "La tabla `reservas`."],
    ["Servidor", "El programa que responde al navegador.", "Laravel recibe las solicitudes."],
],[70,220,CONTENT_W-290]),Spacer(1,7),callout("Recuerda", "Un botón oculto no protege una acción. La autorización importante también debe revisarse en el backend.","gold"),PageBreak()]

# 2 VS CODE
story += [P("2 · Abre el proyecto en Visual Studio Code", "H1N"),P("Antes de buscar archivos, abre la carpeta completa del sistema. Así las búsquedas y el terminal apuntan al proyecto correcto.")]
story += bullets([
    "En VS Code elige <b>Archivo → Abrir carpeta…</b> y selecciona la carpeta del proyecto, la que contiene el archivo `artisan`.",
    "En la barra izquierda, el icono de dos hojas abre el <b>Explorador</b>. Ahí puedes desplegar `app`, `routes`, `resources`, `database` y `tests`.",
    "Pulsa <b>Ctrl+P</b> para abrir un archivo por nombre; escribe, por ejemplo, `usar.blade.php`.",
    "Pulsa <b>Ctrl+Shift+F</b> para buscar texto en todo el proyecto; busca una ruta, un nombre de campo o un método.",
    "Abre <b>Terminal → Nueva terminal</b>. Comprueba que la ruta de la terminal termina en la carpeta del proyecto antes de ejecutar comandos.",
],"BodyN")
story.append(P("Mapa de carpetas que verás en VS Code", "H2N"))
story.append(table([
    ["Carpeta", "Qué suele guardar", "Pregunta para orientarte"],
    ["`routes/`", "Direcciones del sitio.", "¿Qué método atiende esta URL?"],
    ["`app/Http/Controllers/`", "Reglas para cada solicitud.", "¿Qué revisa y qué devuelve?"],
    ["`app/Models/`", "Clases que acceden a datos.", "¿Qué tabla representa?"],
    ["`resources/views/`", "Pantallas Blade.", "¿Qué ve y rellena el usuario?"],
    ["`database/migrations/`", "Cambios de estructura de tablas.", "¿Qué columnas crea el proyecto?"],
    ["`tests/`", "Comprobaciones automáticas.", "¿Qué comportamiento se verifica?"],
],[80,190,CONTENT_W-270]))
story += [Spacer(1,6),callout("No abras un archivo por accidente desde una copia", "Asegúrate de trabajar en la carpeta del proyecto que contiene `artisan`, `composer.json` y `routes/`. Si VS Code muestra otra ruta, cierra la ventana y abre la carpeta correcta.","red"),PageBreak()]

# 3 REQUEST FLOW
story += [P("3 · Sigue una petición con los ojos", "H1N"),P("Ejemplo: alguien abre el calendario de salones. La pantalla no encuentra sola todas las reservas; el navegador pide esos datos al servidor.")]
story.append(PathDiagram([
    ("1 · URL","/salones/usar",BLUE),
    ("2 · RUTA","routes/web.php",TEAL),
    ("3 · CONTROLADOR","SalonController",GOLD),
    ("4 · VISTA","usar.blade.php",colors.HexColor("#7C3AED")),
    ("5 · DATOS","API y PostgreSQL",GREEN),
],height=92))
story += [P("Recorrido concreto", "H2N"),table([
    ["Paso", "Archivo o pieza", "Qué hace"],
    ["1", "`routes/web.php`", "Conecta `/salones/usar` con la acción del calendario."],
    ["2", "`SalonController::calendario()`", "Prepara la vista y los datos de salones."],
    ["3", "`resources/views/salones/usar.blade.php`", "Dibuja la pantalla y ejecuta el JavaScript del calendario."],
    ["4", "`/api/salones/reservas`", "El navegador pide los eventos como datos JSON."],
    ["5", "`ReservaController::obtenerReservas()`", "Consulta reservas y salones y construye la respuesta."],
    ["6", "FullCalendar", "Presenta los eventos en la pantalla."],
],[35,230,CONTENT_W-265])]
story += [Spacer(1,6),callout("Un método es una acción con nombre", "En `ReservaController::obtenerReservas()`, el archivo es el controlador y `obtenerReservas` es la acción que puedes buscar con Ctrl+Shift+F.","green"),PageBreak()]

# 4 FRONTEND BACKEND
story += [P("4 · ¿Qué parte cambio?", "H1N"),P("Busca primero el resultado que quieres. Si es apariencia, revisa la vista y sus estilos. Si es permiso o regla, revisa el backend. Una función completa puede tocar más de un archivo.")]
story.append(table([
    ["Si quieres cambiar…", "Empieza revisando…", "Por qué"],
    ["Texto, botón o distribución", "`resources/views/salones/usar.blade.php`", "Allí está la plantilla de la pantalla."],
    ["Color global o modo oscuro", "`resources/css/app.css`", "Contiene estilos compartidos por la aplicación."],
    ["Una regla de reservas", "`app/Http/Controllers/ReservaController.php`", "Allí se validan y procesan acciones."],
    ["Quién puede eliminar", "Controlador y `routes/web.php`", "El permiso debe comprobarse en el servidor."],
    ["Una columna de base de datos", "Modelo y nueva migración", "El código y la estructura deben coincidir."],
    ["Una petición del navegador", "Vista JavaScript + ruta + controlador", "La pantalla envía datos al servidor."],
],[120,180,CONTENT_W-300]))
story += [P("HTML, Blade, CSS y JavaScript", "H2N"),table([
    ["Tecnología", "Qué significa aquí"],
    ["HTML", "Los campos, títulos y botones que estructuran la pantalla."],
    ["Blade", "Plantilla de Laravel; `{{ }}` muestra datos y `@if` decide si mostrar contenido."],
    ["CSS", "Reglas de aspecto: tamaño, color, separación y adaptación móvil."],
    ["JavaScript", "Acciones en el navegador: abrir un calendario, validar una entrada o enviar una petición."],
    ["JSON", "Formato de texto para que navegador y servidor intercambien datos."],
],[75,CONTENT_W-75])]
story += [Spacer(1,6),callout("Consejo de principiante", "No intentes reorganizar una vista grande completa en tu primer cambio. Localiza una regla pequeña, entiende su función, cambia una cosa y observa el resultado.","blue"),PageBreak()]

# 5 DATABASE
story += [P("5 · Entiende las tablas sin miedo", "H1N"),P("Una tabla se parece a una hoja de cálculo: columnas describen el tipo de dato y cada fila representa un registro. PostgreSQL es el motor de base de datos que usa este proyecto.")]
story.append(PathDiagram([
    ("USUARIOS","Cuentas y roles",BLUE),
    ("RESERVAS","Evento, fechas, responsables",TEAL),
    ("SALONES","Nombre, color, orden",GOLD),
],height=88))
story += [P("Leer un registro", "H2N"),table([
    ["Tabla", "Columna", "Ejemplo inventado", "Qué representa"],
    ["reservas", "`id`", "42", "Número único del registro."],
    ["reservas", "`titulo`", "Visita guiada", "Descripción corta del evento."],
    ["reservas", "`fecha_inicio`", "2026-10-22 08:30", "Cuándo empieza."],
    ["reservas", "`fecha_fin`", "2026-10-22 09:30", "Cuándo termina."],
    ["reservas", "`salon`", "Salón de Honor", "Salón reservado; actualmente se guarda por nombre."],
    ["usuarios", "`rol`", "admin", "Perfil que participa en las reglas de acceso."],
],[72,100,140,CONTENT_W-312])]
story += [P("Qué es una clave", "H2N"),P("La clave primaria (PK) identifica una fila sin repetir, normalmente `id`. Una clave foránea (FK) es una conexión formal de una tabla a otra. En este proyecto, las migraciones no crean una FK entre `reservas.salon` y `salones.nombre`; la relación conceptual se hace por el nombre del salón. Eso significa que hay que comprobar que ese nombre siga coincidiendo.")]
story += [callout("Dónde comprobarlo en VS Code", "Abre `database/migrations/` y busca `Schema::create`, `foreignId`, `unique` y los nombres de columnas. La migración es evidencia de lo que el proyecto quiere crear; compara con la base instalada antes de suponer que ambas coinciden.","gold"),PageBreak()]

# 6 FOLLOW EVENT DATA
story += [P("6 · Cómo viaja una fecha desde el campo", "H1N"),P("Una fecha escrita y una elegida en el minicalendario terminan en el mismo valor para Laravel. La vista convierte el texto legible a un valor de fecha y hora para enviar el formulario.")]
story.append(PathDiagram([
    ("CAMPO","2026/10/22 8:30",BLUE),
    ("VALIDACIÓN","Fecha y hora reales",GOLD),
    ("FORMULARIO","fecha_inicio",TEAL),
    ("CONTROLADOR","Valida y guarda",colors.HexColor("#7C3AED")),
    ("POSTGRESQL","reservas",GREEN),
],height=92))
story += [P("Reglas para revisar", "H2N")] + bullets([
    "El formato visible es `AAAA/MM/DD H:mm` o `AAAA/MM/DD HH:mm`; por ejemplo, `2026/10/22 8:30`.",
    "Las horas deben ir de 0 a 23 y los minutos de 00 a 59. El sistema rechaza fechas imposibles, como el día 30 de febrero.",
    "Al elegir otro día, el campo conserva la hora que ya tenía. Si no tenía hora, el calendario usa 08:30.",
    "El minicalendario compacto muestra días previos y siguientes para completar semanas; el número del mes ayuda a distinguirlos.",
],"BodyN") + [P("La vista que contiene los campos del calendario principal es `resources/views/salones/usar.blade.php`. La pantalla de supervisión tiene formularios equivalentes en `resources/views/salones/supervisar.blade.php`.","BodyN"),callout("Prueba manual guiada", "Abre una reserva de prueba, pulsa el campo para ver el minicalendario, cambia el mes, elige un día y comprueba que la hora no cambie. Después escribe una hora válida y envía solo si estás en un entorno de prueba.","green"),PageBreak()]

# 7 SAFE CHANGE WORKFLOW
story += [P("7 · Haz cambios pequeños y comprensibles", "H1N"),P("Antes de editar, escribe en una frase qué comportamiento quieres modificar. Luego recorre los archivos en orden y no cambies varios temas a la vez.")]
story.append(PathDiagram([
    ("1","Encuentra la pantalla",BLUE),
    ("2","Sigue su ruta",TEAL),
    ("3","Lee el código cercano",GOLD),
    ("4","Cambia una regla",colors.HexColor("#7C3AED")),
    ("5","Comprueba el resultado",GREEN),
],height=86))
story += [P("Lista de comprobación para un cambio", "H2N"),table([
    ["Momento", "Pregunta sencilla"],
    ["Antes", "¿En qué pantalla se nota el problema? ¿Qué archivo la dibuja?"],
    ["Antes", "¿La regla pertenece al servidor, al navegador o a ambos?"],
    ["Durante", "¿Puedo hacer una sola modificación y explicar por qué?"],
    ["Después", "¿La acción funciona con un dato correcto y uno incorrecto?"],
    ["Después", "¿Se ve bien en modo claro/oscuro y en una pantalla pequeña?"],
    ["Guardar", "¿El campo envía lo esperado y el servidor mantiene permisos?"],
],[85,CONTENT_W-85])]
story += [Spacer(1,7),callout("Comentarios con fecha", "Agrega fecha cuando registres una modificación concreta: `// 20/10/2026: acepta hora escrita con uno o dos dígitos.` Evita comentar cada línea; comenta el motivo que no es obvio.","blue"),P("Para distinguir partes, usa el estilo que corresponde: `// comentario` en JavaScript/PHP, `/* comentario */` en CSS y `{{-- comentario --}}` en Blade.","BodyN"),PageBreak()]

# 8 LIBRARIES
story += [P("8 · Composer, npm y las librerías", "H1N"),P("Composer y npm son administradores de paquetes: instalan herramientas para el proyecto. No son lo mismo que el código propio del sistema.")]
story.append(table([
    ["Herramienta", "Qué administra", "Archivos para reconocer"],
    ["Composer", "PHP, Laravel y herramientas del backend.", "`composer.json`, `composer.lock`, `vendor/`"],
    ["npm", "JavaScript, CSS y el proceso de compilación.", "`package.json`, `package-lock.json`, `node_modules/`"],
    ["Vite", "Prepara recursos de frontend para el navegador.", "`vite.config.js`, `resources/`, `public/build/`"],
    ["Tailwind CSS", "Clases utilitarias para dar estilo.", "`tailwind.config.js`, archivos de vistas y CSS"],
    ["FullCalendar", "Dibuja el calendario de reservas.", "Se carga desde CDN en la vista del calendario."],
],[75,200,CONTENT_W-275]))
story += [P("Comandos que podrías encontrar", "H2N"),P("Se escriben en el terminal de VS Code, desde la carpeta donde está `artisan`. Para ejecutar comandos en una base de datos, confirma primero que sea local o de pruebas.","BodyN"),P("composer install<br/>npm install<br/>php artisan serve<br/>npm run dev", "CodeN"),callout("Qué es `vendor/` y `node_modules/`", "Son carpetas generadas con paquetes de terceros. No corrijas un error del sistema editando allí; encuentra la configuración o el código propio del proyecto.","gold"),P("Versiones que aparecen en los archivos del proyecto: PHP 8.2+, Laravel 12, PostgreSQL, Vite 7, Tailwind CSS 3 y Alpine.js 3. Comprueba los archivos lock si necesitas saber la versión exacta instalada.","BodyN"),PageBreak()]

# 9 DEBUG
story += [P("9 · Cuando algo no funciona", "H1N"),P("Un error es una pista. Anota qué hiciste, qué esperabas y qué ocurrió. Cambia una sola cosa después de localizar la primera pista útil.")]
story.append(table([
    ["Síntoma", "Primer lugar para mirar", "Qué buscar"],
    ["La página dice 404", "`routes/web.php`", "¿Está registrada la URL?"],
    ["La página dice 500", "`storage/logs/laravel.log`", "El error y la línea del controlador."],
    ["El calendario aparece vacío", "Herramientas del navegador → Network/Red", "Respuesta de `/api/salones/reservas`."],
    ["Un botón no responde", "Herramientas del navegador → Console/Consola", "Error JavaScript y nombre del archivo."],
    ["No se guarda la fecha", "Campo, formulario y controlador", "Formato, validación y respuesta de servidor."],
    ["Un cambio visual no aparece", "CSS, Vite y caché del navegador", "Si el archivo usado es el correcto y está guardado."],
],[110,190,CONTENT_W-300]))
story += [P("Ruta de revisión en el navegador", "H2N")] + bullets([
    "Pulsa F12 para abrir las herramientas del navegador.",
    "En <b>Console</b>, lee el primer error rojo y copia el mensaje completo.",
    "En <b>Network</b>, recarga la página y abre la solicitud que falló. Revisa su código de respuesta.",
    "No compartas contraseñas, cookies ni tokens cuando compartas capturas o mensajes de error.",
],"BodyN") + [callout("Errores de migración", "Una migración cambia la estructura de la base. No uses `php artisan migrate:fresh` para “arreglar” un error: ese comando puede borrar tablas y datos. Primero confirma la conexión y el estado de las migraciones.","red"),PageBreak()]

# 10 ACCESS + SECURITY
story += [P("10 · Roles, permisos y datos seguros", "H1N"),P("Un rol describe qué acciones permite el servidor. Las reglas pueden variar por operación; no asumas que “administrador” significa acceso ilimitado a todo.")]
story.append(table([
    ["Pieza", "Qué significa", "Dónde estudiar"],
    ["Autenticación", "Confirma quién inició sesión.", "`routes/auth.php`, controladores `Auth/`"],
    ["Autorización", "Decide si esa persona puede hacer una acción.", "Métodos de controladores y middleware."],
    ["Validación", "Comprueba que los datos recibidos tengan forma aceptable.", "Controladores y `app/Http/Requests/`."],
    ["Contraseña", "Debe guardarse protegida con hash, no como texto simple.", "Modelo Usuario y flujo de autenticación."],
    ["CSRF", "Protege formularios web contra solicitudes ajenas.", "Middleware y formularios Laravel."],
],[93,200,CONTENT_W-293]))
story += [P("Hábitos que protegen datos", "H2N")] + bullets([
    "No pegues claves o contraseñas en código, capturas, README o este documento.",
    "No publiques `.env`; usa `.env.example` para mostrar nombres de variables sin secretos.",
    "Antes de eliminar o cambiar datos, confirma el rol, el entorno y el efecto de la acción.",
    "En cambios de permisos, verifica el backend; una condición visual no sustituye la autorización.",
],"BodyN") + [callout("Para aprender con seguridad", "Usa una base de pruebas con datos ficticios para practicar. No ejecutes cambios destructivos en una base compartida o de producción.","red"),PageBreak()]

# 11 LEARNING PLAN
story += [P("11 · Plan de estudio de una semana", "H1N"),P("Dedica una sesión corta por tema. El objetivo es aprender a orientarte, no memorizar cada archivo.")]
story.append(table([
    ["Sesión", "Qué leer", "Ejercicio pequeño"],
    ["1", "`README.md` y esta guía.", "Explica en voz alta qué es frontend y backend."],
    ["2", "`routes/web.php`.", "Encuentra la ruta `/salones/usar`."],
    ["3", "`SalonController.php` y `ReservaController.php`.", "Busca el método que carga el calendario."],
    ["4", "`resources/views/salones/usar.blade.php`.", "Encuentra el campo de fecha y el mini calendario."],
    ["5", "`app/Models/Reserva.php` y migraciones.", "Encuentra qué tabla y columnas usan las reservas."],
    ["6", "`tests/` y `storage/logs/`.", "Lee una prueba y localiza el formato del registro de error."],
    ["7", "Repite el recorrido de una reserva.", "Dibuja URL → ruta → controlador → base → vista."],
],[55,220,CONTENT_W-275]))
story += [P("Preguntas para comprobar que entendiste", "H2N")] + bullets([
    "¿Qué archivo conecta una URL con un controlador?",
    "¿Dónde debería verificarse si la persona puede eliminar una reserva?",
    "¿Qué diferencia hay entre una vista Blade y un modelo?",
    "¿Qué carpeta almacena la estructura deseada de la base de datos?",
],"BodyN") + [Spacer(1,5),callout("Respuestas rápidas", "`routes/`; en el backend; la vista dibuja la pantalla y el modelo representa/consulta datos; `database/migrations/`.","green"),PageBreak()]

# 12 CHEAT SHEET
story += [P("12 · Glosario para tener a mano", "H1N"),table([
    ["Término", "Definición fácil"],
    ["Ruta", "Dirección y método que conducen una solicitud al código adecuado."],
    ["Controlador", "La parte que recibe, valida y decide qué respuesta devolver."],
    ["Modelo", "La clase que representa datos y facilita consultar una tabla."],
    ["Migración", "Instrucciones versionadas para crear o cambiar tablas y columnas."],
    ["Vista Blade", "Plantilla que Laravel convierte en HTML para mostrar una página."],
    ["Endpoint", "Una URL que recibe o devuelve datos; por ejemplo, una ruta API."],
    ["Dependencia", "Una librería externa que el sistema utiliza."],
    ["Commit", "Una fotografía guardada de cambios en Git."],
    ["Prueba", "Una comprobación que ayuda a detectar si una conducta se rompe."],
],[95,CONTENT_W-95])]
story += [P("La ruta mental que conviene recordar", "H2N"),PathDiagram([
    ("PANTALLA","Qué ve la persona",BLUE),
    ("RUTA","A dónde llega",TEAL),
    ("CONTROLADOR","Qué reglas sigue",GOLD),
    ("MODELO","Qué dato usa",colors.HexColor("#7C3AED")),
    ("BASE","Qué queda guardado",GREEN),
],height=88),Spacer(1,3),callout("Referencia del proyecto", "Esta guía complementa `docs/GUIA_DE_CODIGO_PARA_JUNIOR.md` y `README.md`. Revisión basada en el código disponible el 20/10/2026. Las configuraciones reales pueden variar por computadora y entorno.","blue")]

doc=GuideDoc(str(OUT))
doc.build(story)
print(OUT)
