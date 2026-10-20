from pathlib import Path
from xml.sax.saxutils import escape

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import mm
from reportlab.platypus import (
    BaseDocTemplate, Frame, PageTemplate, Paragraph, Spacer, PageBreak,
    Table, TableStyle, Flowable, KeepTogether, HRFlowable,
)


ROOT = Path(__file__).resolve().parents[2]
OUTPUT = ROOT / "output" / "pdf" / "documentacion_tecnica_sistema_reservas.pdf"
PAGE_W, PAGE_H = A4
LEFT = RIGHT = 16 * mm
TOP = 19 * mm
BOTTOM = 16 * mm
CONTENT_W = PAGE_W - LEFT - RIGHT

NAVY = colors.HexColor("#14243A")
BLUE = colors.HexColor("#2563EB")
TEAL = colors.HexColor("#0F9D91")
GOLD = colors.HexColor("#E6A700")
RED = colors.HexColor("#C83D4D")
INK = colors.HexColor("#25364B")
MUTED = colors.HexColor("#64748B")
PALE = colors.HexColor("#F1F5F9")
LINE = colors.HexColor("#D7E0EA")
GREEN = colors.HexColor("#16845B")

styles = getSampleStyleSheet()
styles.add(ParagraphStyle(name="CoverEyebrow", fontName="Helvetica-Bold", fontSize=9, leading=13, textColor=colors.HexColor("#9BC5FF"), spaceAfter=10))
styles.add(ParagraphStyle(name="CoverTitle", fontName="Helvetica-Bold", fontSize=27, leading=32, textColor=colors.white, spaceAfter=14))
styles.add(ParagraphStyle(name="CoverSub", fontName="Helvetica", fontSize=11, leading=17, textColor=colors.HexColor("#D9E6F5")))
styles.add(ParagraphStyle(name="H1Blue", fontName="Helvetica-Bold", fontSize=17, leading=21, textColor=NAVY, spaceBefore=2, spaceAfter=8, keepWithNext=True))
styles.add(ParagraphStyle(name="H2Blue", fontName="Helvetica-Bold", fontSize=11, leading=14, textColor=BLUE, spaceBefore=9, spaceAfter=5, keepWithNext=True))
styles.add(ParagraphStyle(name="BodyTech", fontName="Helvetica", fontSize=8.7, leading=12, textColor=INK, spaceAfter=5))
styles.add(ParagraphStyle(name="BodySmall", fontName="Helvetica", fontSize=7.6, leading=10, textColor=INK, spaceAfter=3))
styles.add(ParagraphStyle(name="Tiny", fontName="Helvetica", fontSize=6.7, leading=8.2, textColor=INK))
styles.add(ParagraphStyle(name="TableHead", fontName="Helvetica-Bold", fontSize=7.1, leading=8.6, textColor=colors.white))
styles.add(ParagraphStyle(name="TableCell", fontName="Helvetica", fontSize=6.9, leading=8.6, textColor=INK))
styles.add(ParagraphStyle(name="TableCellSmall", fontName="Helvetica", fontSize=6.4, leading=7.8, textColor=INK))
styles.add(ParagraphStyle(name="Callout", fontName="Helvetica", fontSize=8.1, leading=11, textColor=INK, leftIndent=2, rightIndent=2))
styles.add(ParagraphStyle(name="CodeText", fontName="Courier", fontSize=7.1, leading=9, textColor=INK, backColor=PALE, borderPadding=5))
styles.add(ParagraphStyle(name="DiagramTitle", fontName="Helvetica-Bold", fontSize=8, leading=10, textColor=NAVY, alignment=TA_CENTER))
styles.add(ParagraphStyle(name="DiagramText", fontName="Helvetica", fontSize=6.8, leading=8.4, textColor=INK, alignment=TA_CENTER))
styles.add(ParagraphStyle(name="DiagramSmall", fontName="Helvetica", fontSize=6.1, leading=7.3, textColor=INK, alignment=TA_CENTER))


def P(text, style="BodyTech"):
    return Paragraph(escape(str(text)).replace("\n", "<br/>"), styles[style])


def Rich(text, style="BodyTech"):
    return Paragraph(text, styles[style])


def bullet(text, style="BodyTech"):
    return Paragraph("<font color='#2563EB'>•</font>&nbsp;&nbsp;" + escape(text), styles[style])


def make_table(rows, widths, font_style="TableCell", header=True, row_bgs=True, repeat=1):
    cooked = []
    for row_index, row in enumerate(rows):
        cooked_row = []
        for value in row:
            if isinstance(value, Flowable):
                cooked_row.append(value)
            else:
                cooked_row.append(Paragraph(escape(str(value)).replace("\n", "<br/>"), styles["TableHead"] if header and row_index == 0 else styles[font_style]))
        cooked.append(cooked_row)
    t = Table(cooked, colWidths=widths, repeatRows=repeat if header else 0, hAlign="LEFT")
    commands = [
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("LEFTPADDING", (0, 0), (-1, -1), 5),
        ("RIGHTPADDING", (0, 0), (-1, -1), 5),
        ("TOPPADDING", (0, 0), (-1, -1), 4),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
        ("GRID", (0, 0), (-1, -1), 0.35, LINE),
    ]
    if header:
        commands += [("BACKGROUND", (0, 0), (-1, 0), NAVY), ("TEXTCOLOR", (0, 0), (-1, 0), colors.white)]
    if row_bgs:
        start = 1 if header else 0
        for r in range(start, len(rows)):
            if (r - start) % 2 == 1:
                commands.append(("BACKGROUND", (0, r), (-1, r), colors.HexColor("#F7F9FC")))
    t.setStyle(TableStyle(commands))
    return t


def callout(title, body, tone="blue"):
    color = {"blue": BLUE, "red": RED, "green": GREEN, "gold": GOLD}.get(tone, BLUE)
    content = Paragraph(f"<b>{escape(title)}</b><br/>{escape(body)}", styles["Callout"])
    t = Table([[content]], colWidths=[CONTENT_W])
    t.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, -1), colors.HexColor("#F7FAFE" if tone == "blue" else "#FFF8F5" if tone == "red" else "#F1FAF5" if tone == "green" else "#FFFBEB")),
        ("BOX", (0, 0), (-1, -1), 0.7, color),
        ("LINEBEFORE", (0, 0), (0, 0), 4, color),
        ("LEFTPADDING", (0, 0), (-1, -1), 10), ("RIGHTPADDING", (0, 0), (-1, -1), 10),
        ("TOPPADDING", (0, 0), (-1, -1), 8), ("BOTTOMPADDING", (0, 0), (-1, -1), 8),
    ]))
    return t


def wrap_text(canvas, text, x, y, width, font="Helvetica", size=7, color=INK, leading=None, align=TA_CENTER):
    pstyle = ParagraphStyle("tmp", fontName=font, fontSize=size, leading=leading or size * 1.25, textColor=color, alignment=align)
    p = Paragraph(escape(text), pstyle)
    w, h = p.wrap(width, 200)
    p.drawOn(canvas, x, y - h)
    return h


class ArchitectureDiagram(Flowable):
    def __init__(self, width=CONTENT_W, height=168):
        super().__init__(); self.width = width; self.height = height

    def draw(self):
        c = self.canv
        def box(x, y, w, h, title, body, fill, stroke):
            c.setFillColor(fill); c.setStrokeColor(stroke); c.setLineWidth(1)
            c.roundRect(x, y, w, h, 7, fill=1, stroke=1)
            wrap_text(c, title, x+6, y+h-10, w-12, "Helvetica-Bold", 8, NAVY, 9, TA_CENTER)
            wrap_text(c, body, x+8, y+h-29, w-16, "Helvetica", 6.6, INK, 8.3, TA_CENTER)
        def arrow(x1, y1, x2, y2, label="", dashed=False):
            c.saveState(); c.setStrokeColor(BLUE); c.setFillColor(BLUE); c.setLineWidth(1.1)
            if dashed: c.setDash(3, 2)
            c.line(x1, y1, x2, y2)
            c.setDash()
            angle = 0 if x2 >= x1 else 3.14159
            c.translate(x2, y2); c.rotate(0 if x2 >= x1 else 180)
            path = c.beginPath(); path.moveTo(0, 0); path.lineTo(-6, 3); path.lineTo(-6, -3); path.close()
            c.drawPath(path, fill=1, stroke=0); c.restoreState()
            if label: wrap_text(c, label, min(x1,x2), min(y1,y2)+10, abs(x2-x1), "Helvetica", 5.8, MUTED, 7, TA_CENTER)
        box(0, 54, 112, 83, "Navegador", "Pantallas HTML/Blade\nFullCalendar\nSolicitudes con sesión", colors.HexColor("#EAF2FF"), BLUE)
        box(156, 39, 205, 113, "Aplicación Laravel", "Rutas y middleware\nControladores y validación\nModelos Eloquent\nVistas Blade y JSON", colors.HexColor("#EAF8F5"), TEAL)
        box(403, 61, 108, 70, "PostgreSQL", "Base de datos\nesquema protocolos\nreservas, salones, usuarios", colors.HexColor("#FFF7E5"), GOLD)
        box(157, 0, 204, 28, "CDN de interfaz", "FullCalendar, Font Awesome y SweetAlert2", colors.HexColor("#F4F0FF"), colors.HexColor("#8B5CF6"))
        arrow(112, 98, 156, 98, "HTTP / sesión")
        arrow(361, 98, 403, 98, "consultas SQL")
        arrow(157, 14, 112, 73, "recursos externos", True)
        wrap_text(c, "El proyecto actual es monolítico: Laravel sirve la pantalla y procesa sus solicitudes.", 12, 164, self.width-24, "Helvetica-Bold", 7, NAVY, 8.5, TA_CENTER)


class ModuleDiagram(Flowable):
    def __init__(self, width=CONTENT_W, height=166):
        super().__init__(); self.width=width; self.height=height

    def draw(self):
        c=self.canv
        cards=[
            (0,94,158,58,"Calendario", "Vista día, semana, mes y disponibilidad", BLUE),
            (176,94,158,58,"Reservas", "Crear, editar, cancelar y papelera", TEAL),
            (353,94,158,58,"Catálogo", "Salones, color, estado y orden", GOLD),
            (0,20,158,58,"Usuarios", "Cuentas, roles y perfil", colors.HexColor("#805AD5")),
            (176,20,158,58,"Protocolo", "Formulario y documento asociado", colors.HexColor("#D84B87")),
            (353,20,158,58,"Autenticación", "Ingreso, sesión y recuperación", colors.HexColor("#475569")),
        ]
        for x,y,w,h,title,body,col in cards:
            c.setFillColor(colors.white); c.setStrokeColor(col); c.setLineWidth(1.2); c.roundRect(x,y,w,h,6,fill=1,stroke=1)
            c.setFillColor(col); c.roundRect(x,y+h-18,w,18,6,fill=1,stroke=0)
            c.rect(x,y+h-18,w,7,fill=1,stroke=0)
            wrap_text(c,title,x+5,y+h-5,w-10,"Helvetica-Bold",8,colors.white,9,TA_CENTER)
            wrap_text(c,body,x+8,y+h-25,w-16,"Helvetica",6.7,INK,8,TA_CENTER)
        wrap_text(c,"Todos los módulos pasan por rutas Laravel; los controladores coordinan modelos y vistas.", 8, 162, self.width-16, "Helvetica", 6.8, MUTED, 8, TA_CENTER)


class ERDiagram(Flowable):
    def __init__(self, width=CONTENT_W, height=192):
        super().__init__(); self.width=width; self.height=height

    def draw(self):
        c=self.canv
        def entity(x,y,w,h,title,fields,accent):
            c.setFillColor(colors.white); c.setStrokeColor(accent); c.setLineWidth(1.2); c.roundRect(x,y,w,h,6,fill=1,stroke=1)
            c.setFillColor(accent); c.roundRect(x,y+h-20,w,20,6,fill=1,stroke=0); c.rect(x,y+h-20,w,7,fill=1,stroke=0)
            wrap_text(c,title,x+4,y+h-5,w-8,"Helvetica-Bold",8,colors.white,9,TA_CENTER)
            yy=y+h-29
            for field in fields:
                height=wrap_text(c,field,x+8,yy,w-16,"Courier",6.3,INK,7.4,TA_LEFT)
                yy-=height+2
        entity(0,57,145,112,"USUARIOS",["id BIGINT PK","correo VARCHAR UNIQUE","rol VARCHAR","contrasena VARCHAR","nombre, telefono, fechas"],BLUE)
        entity(174,25,186,176,"RESERVAS",["id BIGINT PK","salon VARCHAR (texto)","titulo, fechas inicio/fin","responsable, autoridad","tipo, aforo, ingreso","requerimientos JSON","registrado_por (texto)","modificado_por (texto)","eliminado_en (soft delete)"],TEAL)
        entity(389,57,122,112,"SALONES",["id BIGINT PK","nombre VARCHAR UNIQUE","color VARCHAR(7)","activo BOOLEAN","orden INTEGER"],GOLD)
        c.saveState(); c.setStrokeColor(MUTED); c.setLineWidth(1); c.setDash(3,2)
        c.line(145,108,174,108); c.line(360,108,389,108); c.restoreState()
        wrap_text(c,"referencia lógica\npor nombre/correo",142,119,35,"Helvetica",5.1,MUTED,6,TA_CENTER)
        wrap_text(c,"reservas.salon coincide con salones.nombre",357,119,38,"Helvetica",5.0,MUTED,6,TA_CENTER)
        wrap_text(c,"Las migraciones no declaran claves foráneas entre estas tablas.", 10, 14, self.width-20, "Helvetica-Bold", 6.8, RED, 8, TA_CENTER)


class ProcessDiagram(Flowable):
    def __init__(self, width=CONTENT_W, height=235):
        super().__init__(); self.width=width; self.height=height

    def draw(self):
        c=self.canv
        x=18; w=self.width-36; box_h=28
        steps=[
            ("1. Persona selecciona salón, fecha y hora", BLUE),
            ("2. Navegador envía solicitud con sesión y CSRF", TEAL),
            ("3. Laravel valida rol, campos, horario y salón activo", colors.HexColor("#805AD5")),
            ("4. Consulta si ya existe cruce para el mismo salón", GOLD),
            ("5. Sin cruce: guarda en PostgreSQL y responde JSON", GREEN),
            ("6. Calendario vuelve a consultar y presenta el evento", BLUE),
        ]
        y=self.height-37
        for i,(label,col) in enumerate(steps):
            c.setFillColor(colors.white); c.setStrokeColor(col); c.setLineWidth(1.1); c.roundRect(x,y,w,box_h,6,fill=1,stroke=1)
            c.setFillColor(col); c.circle(x+15,y+box_h/2,8,fill=1,stroke=0)
            c.setFillColor(colors.white); c.setFont("Helvetica-Bold",7); c.drawCentredString(x+15,y+box_h/2-2.5,str(i+1))
            wrap_text(c,label,x+30,y+box_h/2+4,w-40,"Helvetica-Bold",7.2,INK,8.5,TA_LEFT)
            if i<len(steps)-1:
                c.setStrokeColor(MUTED); c.line(self.width/2,y,self.width/2,y-9)
                p=c.beginPath(); p.moveTo(self.width/2,y-9); p.lineTo(self.width/2-3,y-5); p.lineTo(self.width/2+3,y-5); p.close(); c.setFillColor(MUTED); c.drawPath(p,fill=1,stroke=0)
            y-=36
        c.setFillColor(colors.HexColor("#FFF5F5")); c.setStrokeColor(RED); c.roundRect(x+2,4,w-4,17,4,fill=1,stroke=1)
        wrap_text(c,"Si hay cruce, Laravel responde 422 y no guarda la reserva.",x+7,17,w-14,"Helvetica-Bold",6.7,RED,8,TA_CENTER)


class PageDocTemplate(BaseDocTemplate):
    def __init__(self, filename):
        super().__init__(filename, pagesize=A4, leftMargin=LEFT, rightMargin=RIGHT, topMargin=TOP, bottomMargin=BOTTOM,
                         title="Documentación técnica - Sistema de reservas de salones",
                         author="Documentación interna del sistema")
        frame=Frame(LEFT,BOTTOM,CONTENT_W,PAGE_H-TOP-BOTTOM,id="normal",leftPadding=0,rightPadding=0,topPadding=0,bottomPadding=0)
        self.addPageTemplates([PageTemplate(id="standard",frames=[frame],onPage=self.draw_page)])

    def draw_page(self, canvas, doc):
        if doc.page == 1: return
        canvas.saveState()
        canvas.setStrokeColor(LINE); canvas.setLineWidth(.55)
        canvas.line(LEFT,PAGE_H-12*mm,PAGE_W-RIGHT,PAGE_H-12*mm)
        canvas.setFont("Helvetica-Bold",7); canvas.setFillColor(NAVY)
        canvas.drawString(LEFT,PAGE_H-9.3*mm,"SISTEMA DE RESERVAS DE SALONES")
        canvas.setFont("Helvetica",7); canvas.setFillColor(MUTED)
        canvas.drawRightString(PAGE_W-RIGHT,PAGE_H-9.3*mm,"Documentación técnica | 20/10/2026")
        canvas.line(LEFT,11*mm,PAGE_W-RIGHT,11*mm)
        canvas.setFont("Helvetica",7); canvas.setFillColor(MUTED)
        canvas.drawString(LEFT,7.4*mm,"Uso de estudio. Datos de producción pendientes de confirmación.")
        canvas.drawRightString(PAGE_W-RIGHT,7.4*mm,f"Página {doc.page}")
        canvas.restoreState()


def section(number, title, lead=None):
    parts=[Paragraph(f"{number:02d}  {escape(title)}",styles["H1Blue"]),
           HRFlowable(width="100%",thickness=1.1,color=BLUE,spaceAfter=7)]
    if lead: parts.append(P(lead))
    return parts


def code_line(text):
    return Paragraph(escape(text),styles["CodeText"])


story=[]

# Portada
story.append(Spacer(1,17*mm))
cover=Table([
    [Paragraph("MANUAL DE ESTUDIO | EDICIÓN TÉCNICA",styles["CoverEyebrow"])],
    [Paragraph("Documentación técnica<br/>del sistema de reservas",styles["CoverTitle"])],
    [Paragraph("Modelos de datos, arquitectura, módulos, procesos, seguridad y operación explicados desde el código existente.",styles["CoverSub"])],
    [Spacer(1,7*mm)],
    [Paragraph("Fecha de revisión del código: 20 de octubre de 2026",styles["CoverSub"])],
    [Paragraph("Referencia Git: 88e0f20 | Aplicación Laravel monolítica",styles["CoverSub"])],
],colWidths=[CONTENT_W-24])
cover.setStyle(TableStyle([
    ("BACKGROUND",(0,0),(-1,-1),NAVY),
    ("LEFTPADDING",(0,0),(-1,-1),18),("RIGHTPADDING",(0,0),(-1,-1),18),
    ("TOPPADDING",(0,0),(-1,-1),5),("BOTTOMPADDING",(0,0),(-1,-1),5),
    ("BOX",(0,0),(-1,-1),0,NAVY),
]))
story.append(cover)
story.append(Spacer(1,9*mm))
story.append(callout("Cómo leer esta guía", "Describe el código y las migraciones que se revisaron. Los puntos marcados como propuesta o pendiente no están configurados todavía. No incluye claves, contraseñas ni datos personales reales.","blue"))
story.append(Spacer(1,7*mm))
story.append(P("Alcance: los 15 documentos técnicos solicitados. Incluye diagramas originales para estudiar la arquitectura, el modelo de datos y el flujo de una reserva.","BodyTech"))
story.append(PageBreak())

# Resumen y estado de los 15 documentos
story += section(0,"Resumen y estado de la documentación", "La aplicación combina interfaz y servidor en un proyecto Laravel. Las responsabilidades están separadas por carpetas, pero el frontend aún no es un proyecto independiente.")
story.append(ArchitectureDiagram())
story.append(Spacer(1,5))
overview=[
 ["N.º","Documento","Estado según repositorio"],
 ["1","DER","Modelo conceptual inferido de migraciones; referencias entre salón y reserva no usan FK."],
 ["2","Diccionario de datos","Columnas principales documentadas desde migraciones; verificar contra la base instalada."],
 ["3-5","Arquitectura, módulos y procesos","Diagramas basados en rutas, controladores, modelos y vistas."],
 ["6","Inventario tecnológico","Versiones contrastadas en manifiestos y archivos lock; SO productivo pendiente."],
 ["7-8","Base de datos, API e integraciones","Objetos y rutas revisados estáticamente; no se consultó una base viva."],
 ["9-12","Instalación, roles, respaldos y seguridad","Instalación local y controles observados; operación productiva pendiente."],
 ["13","Plan de pruebas","Hay pruebas base de autenticación y perfil; no se ejecutaron en esta revisión."],
 ["14-15","Cambios e infraestructura","Historial Git incluido; servidores productivos y respaldos no están inventariados."],
]
story.append(make_table(overview,[35,145,CONTENT_W-180],font_style="TableCellSmall"))
story.append(Spacer(1,6))
story.append(callout("Advertencia de alcance", "Este PDF documenta y revisa el código; no certifica seguridad, disponibilidad, integridad de una base en producción ni despliegue. Los datos marcados PENDIENTE deben confirmarse con el responsable técnico.","gold"))
story.append(PageBreak())

# 1 DER
story += section(1,"Diagrama Entidad-Relación (DER)", "El esquema principal contiene usuarios, reservas y salones. Las relaciones punteadas son referencias lógicas guardadas como texto, no claves foráneas declaradas en las migraciones revisadas.")
story.append(ERDiagram())
story.append(Spacer(1,5))
story.append(P("Lectura del modelo", "H2Blue"))
story.append(bullet("SALONES.nombre se compara con RESERVAS.salon. El controlador también actualiza reservas activas y en papelera cuando renombra un salón."))
story.append(bullet("RESERVAS.registrado_por y RESERVAS.modificado_por guardan texto; no apuntan a USUARIOS.id."))
story.append(bullet("La tabla tokens_restablecimiento usa correo como clave primaria y relaciona la recuperación por correo sin una FK declarada."))
story.append(bullet("RESERVAS.eliminado_en aplica borrado lógico mediante SoftDeletes. La papelera mantiene registros para restaurar."))
story.append(callout("Punto para el estudio", "Una relación visual no siempre es una restricción de la base. El sistema valida varios vínculos en Laravel, pero la base no impide por sí sola todas las referencias huérfanas.","gold"))
story.append(PageBreak())

# 2 Diccionario de datos
story += section(2,"Diccionario de datos", "Tipos y restricciones se deducen de las migraciones de Laravel. VARCHAR usa longitud 255 si la migración usa string() sin longitud explícita; BIGINT corresponde a id().")
story.append(P("Tabla RESERVAS", "H2Blue"))
reservas=[
 ["Columna","Tipo","Nulo","Clave / regla","Descripción"],
 ["id","BIGINT","No","PK","Identificador de la reserva."],
 ["titulo","VARCHAR(255)","No","","Título. El prefijo [CANCELADO] almacena estado."],
 ["fecha_inicio","TIMESTAMP","No","","Inicio del evento."],
 ["fecha_fin","TIMESTAMP","No","","Fin; se valida posterior al inicio."],
 ["salon","VARCHAR(255)","No","Lógica","Nombre del salón; coincide con salones.nombre."],
 ["nombre_usuario","VARCHAR(255)","Sí","","Nombre de quien solicita."],
 ["telefono_usuario","VARCHAR(255)","Sí","","Teléfono del solicitante."],
 ["nombre_autoridad","VARCHAR(255)","Sí","","Autoridad asociada."],
 ["responsable","VARCHAR(255)","Sí","","Responsable del evento."],
 ["tipo_evento","VARCHAR(255)","Sí","","Categoría del evento."],
 ["requerimientos","JSON","Sí","","Lista estructurada de requerimientos."],
 ["aforo","INTEGER","Sí","","Cantidad estimada de personas."],
 ["tipo_ingreso","VARCHAR(255)","Sí","","Modalidad de ingreso."],
 ["nombre_coordinador_externo","VARCHAR(255)","Sí","","Coordinador externo."],
 ["telefono_coordinador_externo","VARCHAR(255)","Sí","","Teléfono del coordinador externo."],
 ["requerimientos_especiales","TEXT","Sí","","Detalle largo de necesidades."],
 ["registrado_por","VARCHAR(255)","Sí","","Nombre o correo que se mostró al registrar."],
 ["coordinador_interno","VARCHAR(255)","Sí","","Coordinador interno."],
 ["modificado_por","VARCHAR(255)","Sí","Auditoría","Actor del último cambio; no hay historial por cambio."],
 ["fecha_creacion","TIMESTAMP","Sí","","Fecha de creación."],
 ["fecha_actualizacion","TIMESTAMP","Sí","","Fecha de última modificación."],
 ["eliminado_en","TIMESTAMP","Sí","Soft delete","NULL indica activo; fecha indica papelera."],
]
story.append(make_table(reservas,[86,81,42,63,CONTENT_W-272],font_style="TableCellSmall"))
story.append(PageBreak())
story += section(2,"Diccionario de datos (continuación)", "Columnas observadas en las migraciones del catálogo y autenticación.")
story.append(P("Tabla SALONES", "H2Blue"))
salones=[
 ["Columna","Tipo","Nulo","Clave / regla","Descripción"],
 ["id","BIGINT","No","PK","Identificador del salón."],
 ["nombre","VARCHAR(255)","No","UNIQUE","Nombre mostrado; único en la tabla."],
 ["color","VARCHAR(7)","No","Validación de app","Color hexadecimal. La app evita repetirlo y no ofrece edición."],
 ["activo","BOOLEAN","No","DEFAULT TRUE","Disponibilidad para nuevas reservas."],
 ["orden","INTEGER","No","DEFAULT 0","Orden en filtros y calendario."],
 ["fecha_creacion","TIMESTAMP","Sí","","Fecha de creación."],
 ["fecha_actualizacion","TIMESTAMP","Sí","","Fecha de actualización."],
]
story.append(make_table(salones,[86,81,42,83,CONTENT_W-292],font_style="TableCellSmall"))
story.append(P("Tablas de USUARIOS y TOKENS", "H2Blue"))
usuarios=[
 ["Tabla.columna","Tipo","Nulo","Clave / valor","Descripción"],
 ["usuarios.id","BIGINT","No","PK","Identificador."],
 ["usuarios.nombre","VARCHAR(255)","No","","Nombre visible."],
 ["usuarios.correo","VARCHAR(255)","No","UNIQUE","Correo para acceso y notificaciones."],
 ["usuarios.telefono","VARCHAR(255)","Sí","","Teléfono de contacto."],
 ["usuarios.rol","VARCHAR(255)","No","DEFAULT funcionario","Rol inicial de la migración. El registro web asigna usuario."],
 ["usuarios.correo_verificado_en","TIMESTAMP","Sí","","Marca de verificación."],
 ["usuarios.contrasena","VARCHAR(255)","No","Hash","Hash, no texto legible."],
 ["usuarios.token_recordar","VARCHAR(100)","Sí","","Token de sesión persistente."],
 ["usuarios.fecha_creacion / fecha_actualizacion","TIMESTAMP","Sí","","Fechas de auditoría básicas."],
 ["tokens_restablecimiento.correo","VARCHAR(255)","No","PK","Correo objetivo de recuperación."],
 ["tokens_restablecimiento.token","VARCHAR(255)","No","Hash","Token de recuperación cifrado/hash."],
 ["tokens_restablecimiento.fecha_creacion","TIMESTAMP","Sí","","Fecha de emisión para controlar vigencia."],
]
story.append(make_table(usuarios,[145,82,42,72,CONTENT_W-341],font_style="TableCellSmall"))
story.append(Spacer(1,5))
story.append(callout("Límite de verificación", "El documento refleja las migraciones del repositorio, no una extracción directa del servidor. Antes de una migración real, comparar columnas y tipos con la base destino.","gold"))
story.append(PageBreak())

# 3 Architecture
story += section(3,"Diagrama de arquitectura del sistema", "La interfaz y la lógica se ejecutan como un proyecto Laravel. PostgreSQL está configurado como motor de datos en config/database.php.")
story.append(ArchitectureDiagram(height=168))
story.append(P("Componentes y comunicación", "H2Blue"))
story.append(make_table([
 ["Componente","Responsabilidad","Ubicación"],
 ["Navegador","Presenta pantallas y envía solicitudes autenticadas.","Cliente web"],
 ["Laravel","Aplica rutas, sesión, permisos, validación y lógica de negocio.","app/, routes/, bootstrap/"],
 ["Blade / Vite","Renderiza HTML y prepara CSS/JavaScript local.","resources/views, resources/css, resources/js"],
 ["PostgreSQL","Persiste usuarios, reservas y catálogo.","Conexión pgsql; schema configurado por DB_SCHEMA"],
 ["CDN","Entrega FullCalendar, iconos y avisos al navegador.","jsDelivr y cdnjs usados desde vistas"],
],[95,245,CONTENT_W-340],font_style="TableCellSmall"))
story.append(callout("Separación futura", "El frontend aún depende de Blade, sesión Laravel y protección CSRF. Publicarlo por separado requiere diseñar autenticación, cookies, CORS y conectividad del backend antes de mover archivos.","blue"))
story.append(PageBreak())

# 4 modules
story += section(4,"Diagrama de módulos", "La vista principal está organizada por función; las rutas conectan cada módulo con sus controladores.")
story.append(ModuleDiagram())
story.append(make_table([
 ["Módulo","Archivo servidor","Pantalla / respuesta"],
 ["Calendario y disponibilidad","SalonController, ReservaController","salones/usar.blade.php y JSON de eventos"],
 ["Supervisión y exportación","ReservaController","salones/supervisar.blade.php, CSV"],
 ["Catálogo y cuentas","SalonController, UsuarioController","salones/usuarios.blade.php"],
 ["Papelera e historial","ReservaController","salones/papelera.blade.php y supervisar_reservas.blade.php"],
 ["Protocolo","ReservaController","protocolo.blade.php y partials/form_protocolo.blade.php"],
 ["Autenticación y perfil","Auth controllers, ProfileController","auth/, profile/"],
],[116,180,CONTENT_W-296],font_style="TableCellSmall"))
story.append(Spacer(1,5))
story.append(P("Carpetas transversales: app/Models describe datos; app/Http/Requests valida formularios; app/Providers conecta adaptadores; config/ guarda ajustes; database/migrations versiona el esquema.","BodySmall"))
story.append(PageBreak())

# 5 flow
story += section(5,"Diagrama de flujo de creación de una reserva", "El flujo muestra la ruta habitual del calendario. Los controladores vuelven a validar datos aunque la interfaz ya haya realizado validaciones.")
story.append(ProcessDiagram())
story.append(P("Flujos relacionados", "H2Blue"))
story.append(make_table([
 ["Proceso","Camino resumido"],
 ["Inicio de sesión","POST /login -> LoginRequest valida y limita intentos -> Laravel inicia sesión -> redirección al dashboard."],
 ["Editar / cancelar","Calendario selecciona evento -> PUT /api/salones/reservas/{id} -> valida rol y campos -> estado sólo superadmin -> actualiza título y actor."],
 ["Papelera","Admin solicita DELETE -> SoftDeletes llena eliminado_en -> admin puede restaurar o vaciar permanentemente."],
 ["Disponible","Mini calendario/campo define fecha -> tabla pide reservas -> calcula huecos por salón -> muestra hasta 10 filas."],
],[110,CONTENT_W-110],font_style="TableCellSmall"))
story.append(PageBreak())

# 6 Tech inventory
story += section(6,"Inventario tecnológico", "Versiones contrastadas en composer.lock y package-lock.json al revisar el repositorio el 20/10/2026. Los valores de CDN se leen de las etiquetas script de las vistas.")
tech=[
 ["Área","Tecnología","Versión declarada / bloqueada","Uso"],
 ["Backend","PHP","Requisito ^8.2","Lenguaje del servidor."],
 ["Backend","Laravel Framework","12.69.3 bloqueado; ^12.0 en composer.json","Rutas, sesión, ORM, validación."],
 ["Backend","Laravel Breeze","2.4.2","Plantillas de autenticación base."],
 ["Backend","PostgreSQL","Motor pgsql; versión del servidor pendiente","Base de datos."],
 ["Frontend build","Node.js","No fijado en el repositorio","Ejecuta npm y Vite; registrar versión del equipo."],
 ["Frontend build","Vite","7.3.6 bloqueado; ^7.0.7 declarado","Compila CSS y JavaScript."],
 ["Frontend","Tailwind CSS","3.4.19 bloqueado; ^3.1.0 declarado","Utilidades CSS."],
 ["Frontend","Alpine.js / Axios","3.17.4 / 1.20.0 bloqueados","Interacciones y solicitudes HTTP."],
 ["Frontend CDN","FullCalendar","6.1.10","Calendario de día, semana y mes."],
 ["Frontend CDN","Font Awesome / SweetAlert2","6.4.0 / @11","Iconos y avisos emergentes."],
 ["Pruebas/formato","PHPUnit / Pint","11.5.56 / 1.30.4","Pruebas PHP y formato."],
 ["Plataforma","Windows local observado","Versión productiva no definida","Despliegue Nginx/Apache/PHP-FPM no está configurado en repo."],
]
story.append(make_table(tech,[80,110,145,CONTENT_W-335],font_style="TableCellSmall"))
story.append(P("Composer administra paquetes PHP desde composer.json / composer.lock. npm administra paquetes del frontend desde package.json / package-lock.json. No editar vendor/ ni node_modules/. Los manifiestos JSON no aceptan comentarios.","BodySmall"))
story.append(PageBreak())

# 7 database docs
story += section(7,"Documentación técnica de la base de datos", "La conexión de ejemplo es PostgreSQL, esquema protocolos. Las migraciones declaran las estructuras centrales y comentarios descriptivos para PostgreSQL.")
story.append(make_table([
 ["Objeto","Definido / uso observado","Estado"],
 ["Tablas de negocio","usuarios, reservas, salones, tokens_restablecimiento","Definidas en migraciones."],
 ["Historial de migraciones","historial_migraciones con id_migracion, nombre_migracion, lote","Repositorio Laravel adaptado a nombres españoles."],
 ["Claves e índices","PK en id/correo; UNIQUE en usuarios.correo y salones.nombre","Sin índices manuales ni FK en migraciones revisadas."],
 ["Borrado lógico","reservas.eliminado_en + SoftDeletes","Papelera de reservas."],
 ["Tabla eventos","Comentada como histórica; agenda actual consulta reservas","Estructura no creada por migraciones actuales; verificar base existente."],
 ["Cache y colas","Configuración de ejemplo usa archivo/sync","Migraciones vacías desactivan tablas DB de cache y colas."],
 ["Vistas SQL / funciones / procedimientos / triggers","No se encontraron definiciones en migraciones versionadas","Verificar objetos reales en PostgreSQL antes de producción."],
],[105,245,CONTENT_W-350],font_style="TableCellSmall"))
story.append(P("Puntos de integridad", "H2Blue"))
story.append(bullet("Los conflictos de horario se comprueban en ReservaController, antes de guardar. No existe una exclusión de solapamientos declarada en PostgreSQL."))
story.append(bullet("El color único y la protección de bajas del catálogo se validan en Laravel; la migración sólo fija UNIQUE para el nombre."))
story.append(bullet("La tabla reservas.salon no tiene FK a salones. Los nombres deben conservarse sincronizados por reglas de la aplicación."))
story.append(callout("Incidente previo que explica el adaptador", "El repositorio de migraciones traduce nombres internos migration/batch a nombre_migracion/lote. Si una base tiene historial_migraciones con otra estructura, artisan migrate falla; revisar el esquema real antes de ejecutar migraciones.","gold"))
story.append(PageBreak())

# 8 API
story += section(8,"API e integraciones", "No existe routes/api.php para estos endpoints: se declaran en routes/web.php dentro del middleware auth. Usan sesión Laravel y protección CSRF web para cambios.")
api=[
 ["Método y ruta","Entrada principal","Respuesta / acceso"],
 ["GET /api/salones/reservas","Sin filtro obligatorio; eventos activos","JSON de eventos para FullCalendar; sesión requerida."],
 ["GET /api/salones/reservas/{id}","id en URL","Reserva JSON; 404 si no existe."],
 ["POST /api/salones/reservas","titulo, fecha_inicio, fecha_fin, salon; datos de contacto opcionales","200 {success:true}; 403 rol; 422 validación o cruce."],
 ["PUT /api/salones/reservas/{id}","Campos editables; estado opcional","200 éxito; sólo superadmin puede enviar estado; 403/404/422 según caso."],
 ["DELETE /api/salones/reservas/{id}","id en URL","Admin; soft delete; JSON {success:true}; 403 si otro rol."],
 ["GET /api/reservas/fechas","month, year, salon opcional","JSON de fechas ocupadas del mini calendario."],
]
story.append(make_table(api,[151,171,CONTENT_W-322],font_style="TableCellSmall"))
story.append(P("Integraciones externas", "H2Blue"))
story.append(make_table([
 ["Integración","Uso","Estado"],
 ["jsDelivr","FullCalendar 6.1.10 y locale español; SweetAlert2 @11","CDN desde el navegador."],
 ["cdnjs","Font Awesome 6.4.0","CDN desde el navegador."],
 ["Correo","MAIL_MAILER=log en .env.example","No hay proveedor de correo productivo confirmado."],
 ["AWS / pagos / otros servicios","Variables de muestra vacías; no aparece integración funcional revisada","Pendiente confirmar si existen fuera del repositorio."],
],[95,255,CONTENT_W-350],font_style="TableCellSmall"))
story.append(PageBreak())

# 9 installation deployment
story += section(9,"Instalación y despliegue", "Los pasos siguientes son para desarrollo local. Producción requiere decidir servidor, dominio, HTTPS, política de respaldo y método de despliegue.")
story.append(P("Requisitos comprobables", "H2Blue"))
for item in ["PHP compatible con ^8.2 y extensiones requeridas por Laravel.","Composer para dependencias PHP.","Node.js y npm para compilar recursos; versión instalada aún debe fijarse en un manual operativo.","PostgreSQL accesible; crear base y esquema según decisión de instalación.","Acceso a Internet para instalar paquetes y, en el navegador, descargar recursos CDN."]:
    story.append(bullet(item))
story.append(P("Desarrollo local", "H2Blue"))
story.append(code_line("composer install")); story.append(Spacer(1,3))
story.append(code_line("Copy-Item .env.example .env")); story.append(Spacer(1,3))
story.append(code_line("php artisan key:generate")); story.append(Spacer(1,3))
story.append(code_line("npm install")); story.append(Spacer(1,3))
story.append(code_line("php artisan migrate")); story.append(Spacer(1,3))
story.append(code_line("npm run dev   # en otra terminal: php artisan serve"))
story.append(P("Antes de migrar, verificar DB_DATABASE, DB_SCHEMA, DB_HOST y el historial de migraciones. Nunca usar migrate:fresh en una base con datos.","BodySmall"))
story.append(P("Puesta en producción propuesta", "H2Blue"))
story.append(make_table([
 ["Paso","Control"],
 ["Preparar servidor","PHP-FPM con Nginx o Apache; raíz pública debe ser public/. El servidor productivo aún no está definido."],
 ["Proteger configuración","APP_ENV=production, APP_DEBUG=false, APP_URL HTTPS y secretos sólo en entorno del servidor."],
 ["Compilar","composer install --no-dev --optimize-autoloader; npm ci; npm run build."],
 ["Base de datos","Crear usuario PostgreSQL con privilegios mínimos; comprobar esquema y respaldo antes de migrate --force."],
 ["Caché / acceso","config:cache, route:cache, view:cache; HTTPS, firewall, CORS cerrado si se separa frontend."],
 ["Validar salida","Probar login, crear/editar, colisiones, roles, papelera, protocolo, exportación y recuperación."],
],[108,CONTENT_W-108],font_style="TableCellSmall"))
story.append(PageBreak())

# 10 role matrix
story += section(10,"Matriz de roles y permisos", "Matriz basada en las comprobaciones que aparecen en controladores y rutas al 20/10/2026. Se distingue permiso explícito de permiso implícito por sólo exigir login.")
roles=[
 ["Acción","usuario / funcionario","admin","superadmin","Control actual"],
 ["Abrir rutas protegidas","Sí","Sí","Sí","Middleware auth; algunas rutas sólo requieren sesión."],
 ["Crear / editar reserva calendario","No en API","Sí","Sí","ReservaController verifica admin/superadmin."],
 ["Cambiar estado cancelado","No","No","Sí","Sólo superadmin cuando se envía estado."],
 ["Eliminar reserva / vaciar papelera","No","Sí","No","Métodos exigen exactamente admin."],
 ["Ver papelera","No","Sí","No","Método exige admin."],
 ["Restaurar reserva","usuario: no","Sí","Sí","Bloquea usuario/vacío; otros roles no estándar podrían pasar."],
 ["Administrar nombres/color/estado salones","No","Sí","Sí","Validador permite ambos roles; no sólo superadmin."],
 ["Crear/editar/eliminar cuentas","No","Sí","No","UsuarioController verifica admin exactamente."],
 ["Ver usuarios/supervisión/exportar","Revisar","Sí","Sí","Varias acciones no tienen guardia de rol en controlador."],
 ["Eliminar salón del catálogo","Revisar","Revisar","Revisar","destroy() no verifica rol ni reservas antes del delete físico."],
]
story.append(make_table(roles,[120,83,58,78,CONTENT_W-339],font_style="TableCellSmall"))
story.append(Spacer(1,6))
story.append(callout("Acción recomendada antes de producción", "Alinear las reglas con la matriz aprobada: proteger SalonController.destroy, revisar acceso a listas de usuarios/supervisión/exportación y al formulario web de creación. Confirmar además si admin o sólo superadmin administra el catálogo. No se modificó código como parte de este documento.","red"))
story.append(Spacer(1,5))
story.append(P("Nota de roles: la migración da rol inicial funcionario, pero el registro web asigna usuario. Revisar cuentas existentes y normalizar nombres de rol antes de confiar en reglas por texto.","BodySmall"))
story.append(PageBreak())

# 11 backups
story += section(11,"Plan de copias de seguridad y recuperación", "No se encontró una tarea automática de respaldo versionada. Lo siguiente es una propuesta, no una operación activa.")
story.append(make_table([
 ["Elemento","Propuesta inicial","Estado actual"],
 ["Respaldo","pg_dump en formato custom, diario antes de ventana de cambios.","No configurado en el repositorio."],
 ["Retención","14 diarios y 12 mensuales; ajustar a política institucional.","Responsable y retención pendientes."],
 ["Ubicación","Copia cifrada fuera del servidor de base de datos, acceso restringido.","Servidor/almacenamiento productivo pendiente."],
 ["Verificación","Restauración mensual aislada; simulacro trimestral documentado.","Sin resultados de recuperación registrados."],
 ["Responsable","Administrador de infraestructura designado por la institución.","Nombre y suplente pendientes."],
],[91,235,CONTENT_W-326],font_style="TableCellSmall"))
story.append(P("Ejemplo para crear respaldo", "H2Blue"))
story.append(code_line("pg_dump --format=custom --file=reservas_YYYYMMDD.dump --dbname=sistema_certificados"))
story.append(Spacer(1,4))
story.append(P("Ejemplo conceptual de restauración en una base de pruebas vacía. No ejecutar contra producción sin autorización y plan de recuperación aprobado:","BodySmall"))
story.append(code_line("pg_restore --clean --if-exists --dbname=sistema_eventos_restore reservas_YYYYMMDD.dump"))
story.append(Spacer(1,5))
story.append(callout("Datos de acceso", "No guardar contraseñas en el PDF, scripts o historial de terminal. Usar el gestor institucional de secretos o el mecanismo seguro aprobado para el servidor.","gold"))
story.append(PageBreak())

# 12 security
story += section(12,"Documentación de seguridad", "Resumen de controles observados en código. No representa una prueba de penetración ni una auditoría completa de dependencias.")
story.append(make_table([
 ["Área","Control observado","Pendiente / nota"],
 ["Sesión","Rutas agrupadas con auth; dashboard requiere verificación de correo.","Confirmar expiración, cookies seguras y HTTPS en despliegue."],
 ["Contraseñas","Modelo usa cast hashed; controladores usan Hash::make.","Política de longitud y recuperación debe revisarse con la institución."],
 ["Fuerza bruta","LoginRequest bloquea tras cinco intentos por correo e IP.","Verificar límite y monitoreo en entorno productivo."],
 ["Validación","Controladores validan fechas, salón, campos y conflictos.","Completar pruebas de rol y solicitudes alternativas."],
 ["CSRF","Endpoints web usan sesión Laravel y middleware web.","No publicar como API independiente sin diseñar autenticación/CORS."],
 ["Auditoría","modificado_por almacena actor de último cambio.","No hay historial append-only de cada modificación."],
 ["Secretos",".env está ignorado por Git; .env.example contiene valores de muestra.","Rotar cualquier secreto compartido por canales no seguros."],
 ["Correo","Ejemplo usa mailer log.","No hay transportador de correo productivo confirmado."],
],[80,220,CONTENT_W-300],font_style="TableCellSmall"))
story.append(P("Observaciones estáticas de permisos", "H2Blue"))
for item in [
    "SalonController.destroy ejecuta eliminación física de un salón sin validar rol ni comprobar reservas asociadas.",
    "UsuarioController.index carga todas las cuentas y no revisa rol en el método; la ruta exige sesión, no rol.",
    "ReservaController.supervisar, supervisarReservas y exportarCsv no muestran una verificación de rol en el método revisado.",
    "guardarReservaCompleta no contiene el mismo control explícito admin/superadmin que el endpoint JSON del calendario.",
    "La autorización del catálogo permite admin y superadmin, a pesar de que el método se llama validarSuperadmin.",
]: story.append(bullet(item,"BodySmall"))
story.append(callout("Prioridad", "Resolver y probar los controles de autorización y eliminación física antes de exponer el sistema fuera de la red local. La interfaz no reemplaza controles del servidor.","red"))
story.append(PageBreak())

# 13 testing plan
story += section(13,"Plan de pruebas técnicas", "Se revisó el inventario de tests, pero no se ejecutó PHPUnit para este PDF. La columna Resultado queda pendiente de ejecución y evidencia.")
tests=[
 ["ID","Caso sugerido","Resultado esperado","Resultado"],
 ["T-01","Ingresar válido e inválido; bloqueo tras 5 intentos.","Sesión válida o error controlado / throttle.","Pendiente"],
 ["T-02","Crear evento con intervalo válido.","JSON de éxito y fila persistida.","Pendiente"],
 ["T-03","Crear evento con fin antes del inicio.","Validación 422; no se guarda.","Pendiente"],
 ["T-04","Intentar cruce horario en el mismo salón.","Respuesta 422; no hay duplicidad.","Pendiente"],
 ["T-05","Repetir horario en salones diferentes.","Se permite si reglas lo autorizan.","Pendiente"],
 ["T-06","Cambiar a cancelado como superadmin y como admin.","Superadmin permite; admin recibe 403.","Pendiente"],
 ["T-07","Eliminar, restaurar y vaciar papelera con roles.","Sólo roles autorizados; datos coherentes.","Pendiente"],
 ["T-08","Mini calendario cambia fecha en Disponible.","Una fecha seleccionada y máximo 10 filas.","Pendiente"],
 ["T-09","Ancho de pantalla móvil, día/semana/mes.","Controles utilizables y datos accesibles.","Pendiente"],
 ["T-10","Respaldar y restaurar en base aislada.","Recuperación íntegra documentada.","Pendiente"],
]
story.append(make_table(tests,[42,190,190,CONTENT_W-422],font_style="TableCellSmall"))
story.append(P("Cobertura existente", "H2Blue"))
story.append(P("Hay pruebas base de autenticación, contraseñas, verificación y perfil en tests/Feature. No se encontraron suites específicas para reservas, catálogo de salones, roles de administración, papelera ni API del calendario.","BodySmall"))
story.append(PageBreak())

# 14 versions
story += section(14,"Historial de versiones y cambios", "Resumen de los últimos commits disponibles al revisar Git. No se identificaron etiquetas de versión semántica en esta revisión.")
history=[
 ["Commit","Fecha","Asunto"],
 ["88e0f20","20/10/2026","Actualización del proyecto"],
 ["7427b99","19/10/2026","Actualicé la vista de disponibles"],
 ["3d914de","19/10/2026","Merge pull request #2 desde versión local"],
 ["24b7a9a","19/10/2026","Descripción de las actualizaciones realizadas"],
 ["9e6b047","16/10/2026","Actualicé la exportación de secciones"],
 ["8dcc8fb","16/10/2026","Merge pull request #1 desde versión local"],
 ["362b73b","16/10/2026","Actualización del proyecto"],
 ["311e56e","16/10/2026","Actualización del proyecto"],
]
story.append(make_table(history,[75,90,CONTENT_W-165],font_style="TableCellSmall"))
story.append(Spacer(1,6))
story.append(callout("Estado del árbol de trabajo", "La revisión encontró archivos modificados y documentación local sin confirmar en Git. El hash 88e0f20 identifica HEAD, no incluye por sí solo todos los cambios locales visibles al generar este PDF.","gold"))
story.append(P("Para cada cambio futuro, registrar fecha, persona responsable, motivo, archivos afectados y pruebas ejecutadas. Crear una etiqueta de versión sólo cuando exista un proceso de publicación acordado.","BodySmall"))
story.append(PageBreak())

# 15 infrastructure
story += section(15,"Inventario de infraestructura", "Se separan valores locales de los datos productivos. Las direcciones privadas, credenciales y certificados deben guardarse en un inventario restringido, no en este PDF.")
infra=[
 ["Elemento","Desarrollo observado / ejemplo","Producción"],
 ["URL navegador","http://127.0.0.1:8007","Dominio y URL HTTPS: PENDIENTE"],
 ["Laravel","php artisan serve en desarrollo","Servidor web/PHP-FPM: PENDIENTE"],
 ["PostgreSQL","DB_HOST 127.0.0.1, DB_PORT 5432 en .env.example","Host, puerto y HA: PENDIENTE"],
 ["Base / esquema","sistema_certificados / protocolos en ejemplo","Nombre real y permisos: PENDIENTE"],
 ["Frontend assets","Vite local o public/build","CDN permitido / assets locales: PENDIENTE"],
 ["TLS / certificado","No configurado en el repositorio","Emisor, vencimiento y renovación: PENDIENTE"],
 ["Firewall / IPs privadas","No inventariado","Restringir a documento de acceso controlado."],
 ["Respaldos / monitoreo","Sin automatización documentada","Destino, alertas y responsables: PENDIENTE"],
]
story.append(make_table(infra,[95,194,CONTENT_W-289],font_style="TableCellSmall"))
story.append(P("Registro restringido complementario", "H2Blue"))
story.append(P("En el repositorio institucional restringido se debe guardar: responsable y contacto de guardia, IP interna, hostname, sistema operativo y parcheado, puertos expuestos, firewall, certificados, cuentas técnicas, bóveda de secretos, retención de respaldos, proveedor y fecha de renovación. No incluir contraseñas ni llaves privadas en la copia general de estudio.","BodySmall"))
story.append(PageBreak())

# Learning guide and glossary
story += section(16,"Ruta de estudio y glosario", "Sugerencia para aprender el sistema desde una tarea concreta hasta el almacenamiento.")
study=[
 ["Orden","Lee / observa","Pregunta de aprendizaje"],
 ["1","routes/web.php","¿Qué URL llama a qué método?"],
 ["2","app/Http/Controllers/ReservaController.php","¿Qué valida el servidor antes de responder?"],
 ["3","app/Models/Reserva.php y migraciones","¿Qué columnas se guardan y cómo se convierten fechas/JSON?"],
 ["4","resources/views/salones/usar.blade.php","¿Qué HTML, estilos y eventos ve la persona?"],
 ["5","DevTools del navegador / Network","¿Qué endpoint responde y qué JSON entrega?"],
 ["6","tests/Feature","¿Qué comportamiento se comprueba automáticamente?"],
 ["7","Git log y diff","¿Qué archivos y motivo corresponden a la versión?"],
]
story.append(make_table(study,[48,205,CONTENT_W-253],font_style="TableCellSmall"))
story.append(P("Glosario corto", "H2Blue"))
story.append(make_table([
 ["Término","Significado"],
 ["Ruta","Regla que conecta una URL y método HTTP con una acción."],
 ["Controlador","Código del backend que valida, consulta y responde."],
 ["Modelo","Clase Laravel que representa una tabla."],
 ["Migración","Archivo versionado que crea o cambia el esquema de datos."],
 ["Blade","Plantilla Laravel que mezcla HTML con datos y directivas."],
 ["Soft delete","Marca de eliminación lógica que conserva la fila."],
 ["CSRF","Protección contra solicitudes web falsificadas usando sesión."],
 ["CDN","Servidor externo que entrega una librería al navegador."],
],[100,CONTENT_W-100],font_style="TableCellSmall"))
story.append(Spacer(1,6))
story.append(callout("Cambios de esta documentación", "20/10/2026: se preparó este manual a partir de rutas, controladores, modelos, vistas, migraciones, manifiestos de dependencias, archivos lock, pruebas existentes y Git. Se añadieron diagramas de arquitectura, módulos, DER y flujo de creación.","green"))


OUTPUT.parent.mkdir(parents=True, exist_ok=True)
doc=PageDocTemplate(str(OUTPUT))
doc.build(story)
print(OUTPUT)
