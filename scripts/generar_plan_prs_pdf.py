from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER
from reportlab.lib.pagesizes import letter, landscape
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import inch
from reportlab.platypus import (
    KeepTogether,
    ListFlowable,
    ListItem,
    PageBreak,
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)


ROOT = Path(__file__).resolve().parents[1]
OUT_DIR = ROOT / "docs"
OUT_DIR.mkdir(exist_ok=True)
PDF_PATH = OUT_DIR / "plan_pull_requests_gitflow_manejo_buses.pdf"


PRS = [
    ("PR-01", "A", "feature/base-configuracion-db", "Base Laravel, .env.example, conexion MySQL, migraciones y modelos iniciales.", "Proyecto corre, migraciones ejecutan, php artisan test pasa."),
    ("PR-02", "B", "feature/interfaz-principal", "Layout principal, menu publico, pagina de inicio de buses y estructura visual general.", "Inicio separado del login, menu responsive, pagina publica en /."),
    ("PR-03", "A", "feature/autenticacion-usuarios", "Registro, login, logout, dashboard protegido y roles base.", "Usuarios nuevos quedan como cliente, rutas protegidas por auth."),
    ("PR-04", "A", "feature/crud-cooperativas", "CRUD de cooperativas con datos de contacto, logo path y estado activo.", "Se puede crear, listar, editar, ver y eliminar cooperativas."),
    ("PR-05", "B", "feature/crud-ciudades", "CRUD de ciudades y provincias para origen, destino y paradas.", "Ciudades activas disponibles para rutas y frecuencias."),
    ("PR-06", "A", "feature/crud-buses", "CRUD de buses asociado a cooperativas.", "Numero unico por cooperativa, placa unica, filtros basicos."),
    ("PR-07", "B", "feature/tipos-asientos-asientos", "CRUD de tipos de asiento y asientos por bus.", "Buses tienen configuracion de asientos Normal/VIP y capacidad consistente."),
    ("PR-08", "A", "feature/crud-rutas", "CRUD de rutas relacionado con cooperativas, buses, origen y destino.", "Ruta valida origen distinto de destino y bus opcional."),
    ("PR-09", "A", "feature/frecuencias-ant-paradas", "CRUD de frecuencias ANT y paradas intermedias ordenadas.", "Resolucion ANT, hora, estado y paradas en orden."),
    ("PR-10", "B", "feature/salidas-hoja-ruta", "Generacion manual de salidas/hoja de ruta desde frecuencias habilitadas.", "Salida asigna bus, fecha, hora, precio base y estado."),
    ("PR-11", "B", "feature/busqueda-viajes", "Pantalla publica para buscar destinos y frecuencias disponibles.", "Filtros por cooperativa, tipo asiento, chasis/carroceria y tipo de viaje."),
    ("PR-12", "B", "feature/seleccion-asientos", "Seleccion de asientos disponibles por salida y tipo.", "No permite vender asiento ocupado en la misma salida."),
    ("PR-13", "B", "feature/venta-boletos-cliente", "Compra de boletos para cliente final con descuentos.", "Registra boleto con origen/destino, pasajero, asiento, precio y estado."),
    ("PR-14", "A", "feature/venta-boletos-oficinista", "Venta interna para rol oficinista.", "Oficinista puede vender/validar boletos desde panel protegido."),
    ("PR-15", "B", "feature/pagos-comprobantes", "Metodo deposito/transferencia y carga de comprobante.", "Pago queda pendiente y puede validarse manualmente."),
    ("PR-16", "A", "feature/validacion-pagos", "Validacion/rechazo de pagos por oficinista.", "Actualiza estado del pago y del boleto segun validacion."),
    ("PR-17", "B", "feature/boleto-pdf-codigo", "Generar boleto descargable con codigo QR o codigo de barras.", "Boleto visible despues de compra y desde historial."),
    ("PR-18", "B", "feature/historial-compras", "Historial de compras para usuario cliente.", "Cliente ve boletos, estados, pagos y descarga boleto."),
    ("PR-19", "A", "feature/acceso-pasajeros", "Modulo para chofer/ayudante: escaneo o ingreso de codigo.", "Registra acceso permitido/rechazado en registro_accesos."),
    ("PR-20", "A", "feature/configuracion-aplicacion", "CRUD/configuracion de logo, colores, redes, soporte.", "Configuracion se refleja en layout cuando aplique."),
    ("PR-21", "A", "feature/roles-permisos", "Middleware y restricciones por rol.", "Cooperativa gestiona buses propios, oficinista ventas, cliente compras."),
    ("PR-22", "B", "feature/notificaciones-correo", "Notificaciones por correo o app en pasos relevantes.", "Compra, pago pendiente, pago validado y boleto emitido notifican."),
    ("PR-23", "A+B", "feature/reportes-auditoria", "Reportes para evidenciar trabajo, cambios y estados del sistema.", "Dashboard o vistas con resumen de ventas, pagos, accesos y auditoria."),
    ("PR-24", "A+B", "release/v1.0.0", "Integracion final, QA, documentacion, informe y preparacion de presentacion.", "Release estable en main con tag y pruebas finales."),
]


def styles():
    base = getSampleStyleSheet()
    base.add(ParagraphStyle(name="CenterTitle", parent=base["Title"], alignment=TA_CENTER, fontName="Helvetica-Bold", fontSize=20, leading=24, textColor=colors.HexColor("#0F172A")))
    base.add(ParagraphStyle(name="SubtitleCenter", parent=base["Normal"], alignment=TA_CENTER, fontSize=10, leading=13, textColor=colors.HexColor("#334155")))
    base.add(ParagraphStyle(name="H1Custom", parent=base["Heading1"], fontName="Helvetica-Bold", fontSize=14, leading=17, textColor=colors.HexColor("#0F172A"), spaceBefore=10, spaceAfter=6))
    base.add(ParagraphStyle(name="H2Custom", parent=base["Heading2"], fontName="Helvetica-Bold", fontSize=11, leading=14, textColor=colors.HexColor("#1E3A5F"), spaceBefore=8, spaceAfter=4))
    base.add(ParagraphStyle(name="BodyCustom", parent=base["BodyText"], fontSize=9, leading=12, spaceAfter=5))
    base.add(ParagraphStyle(name="Small", parent=base["BodyText"], fontSize=7.4, leading=9))
    base.add(ParagraphStyle(name="CodeBlock", parent=base["Code"], fontName="Courier", fontSize=7.8, leading=9.5, backColor=colors.HexColor("#F1F5F9"), borderPadding=5, leftIndent=4, rightIndent=4))
    return base


S = styles()


def p(text, style="BodyCustom"):
    return Paragraph(text.replace("&", "&amp;"), S[style])


def bullets(items):
    return ListFlowable([ListItem(p(item), leftIndent=12) for item in items], bulletType="bullet", start="circle", leftIndent=14)


def numbers(items):
    return ListFlowable([ListItem(p(item), leftIndent=12) for item in items], bulletType="1", leftIndent=14)


def code(lines):
    escaped = "<br/>".join(line.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;") for line in lines)
    return Paragraph(escaped, S["CodeBlock"])


def table(data, col_widths, font_size=7.4):
    converted = []
    for row in data:
        converted.append([Paragraph(str(cell).replace("&", "&amp;"), S["Small"]) for cell in row])
    t = Table(converted, colWidths=col_widths, repeatRows=1, hAlign="LEFT")
    t.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#D9EAF7")),
        ("TEXTCOLOR", (0, 0), (-1, 0), colors.HexColor("#0F172A")),
        ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
        ("FONTSIZE", (0, 0), (-1, -1), font_size),
        ("GRID", (0, 0), (-1, -1), 0.35, colors.HexColor("#CBD5E1")),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("LEFTPADDING", (0, 0), (-1, -1), 4),
        ("RIGHTPADDING", (0, 0), (-1, -1), 4),
        ("TOPPADDING", (0, 0), (-1, -1), 4),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
    ]))
    return t


def build_pdf():
    doc = SimpleDocTemplate(
        str(PDF_PATH),
        pagesize=landscape(letter),
        rightMargin=0.45 * inch,
        leftMargin=0.45 * inch,
        topMargin=0.45 * inch,
        bottomMargin=0.45 * inch,
        title="Plan de Pull Requests con Git Flow - Manejo Buses",
    )
    story = []

    story.append(Paragraph("Plan de Pull Requests con Git Flow", S["CenterTitle"]))
    story.append(Paragraph("Proyecto: Manejo Buses - Gestion y venta de pasajes interprovinciales", S["SubtitleCenter"]))
    story.append(Paragraph("Rama base: develop | Equipo: 2 personas | Flujo: Git Flow", S["SubtitleCenter"]))
    story.append(Spacer(1, 0.18 * inch))

    story.append(Paragraph("1. Objetivo", S["H1Custom"]))
    story.append(p("Este documento divide el desarrollo completo del proyecto en Pull Requests manejables para dos personas. La division esta pensada para trabajar desde develop usando Git Flow, evidenciar colaboracion, mantener cambios revisables y facilitar la auditoria de configuracion solicitada en el segundo parcial."))

    story.append(Paragraph("2. Reglas generales de Git Flow", S["H1Custom"]))
    story.append(bullets([
        "develop es la base de integracion diaria.",
        "Cada tarea se trabaja en una rama feature/* creada desde develop.",
        "Cada Pull Request apunta hacia develop, nunca directo a main.",
        "Antes de abrir PR se ejecuta php artisan test y, si cambia frontend, npm run build.",
        "Cada PR debe vincularse a una tarea o cambio en Jira.",
        "La otra persona revisa el PR antes del merge.",
        "main se usa solo para versiones estables mediante release/* y hotfix/*.",
    ]))

    story.append(Paragraph("3. Comandos base para cualquier PR", S["H1Custom"]))
    story.append(code([
        "git checkout develop",
        "git pull origin develop",
        "git checkout -b feature/nombre-de-la-tarea",
        "# realizar cambios",
        "php artisan test",
        "npm run build",
        "git status",
        "git add .",
        "git commit -m \"feat: descripcion corta de la tarea\"",
        "git push -u origin feature/nombre-de-la-tarea",
        "# abrir Pull Request hacia develop en GitHub",
    ]))

    story.append(Paragraph("4. Division general por persona", S["H1Custom"]))
    story.append(table([
        ["Persona", "Responsabilidad principal", "Responsabilidad secundaria"],
        ["Persona A", "Base tecnica, autenticacion, usuarios internos, cooperativas, buses, rutas/frecuencias, panel administrativo.", "Revisar PRs de ventas, pagos, boletos y notificaciones."],
        ["Persona B", "Interfaz publica, busqueda de viajes, compra de boletos, pagos, comprobantes, historial y validacion de acceso.", "Revisar PRs de configuracion, modelos, migraciones y administracion."],
    ], [1.1 * inch, 4.2 * inch, 4.2 * inch]))

    story.append(Paragraph("5. Roadmap por Pull Request", S["H1Custom"]))
    roadmap = [["PR", "Resp.", "Rama", "Alcance", "Criterio de aceptacion"]] + [list(row) for row in PRS]
    story.append(table(roadmap, [0.55 * inch, 0.45 * inch, 1.85 * inch, 3.5 * inch, 3.0 * inch], font_size=6.8))

    story.append(PageBreak())
    story.append(Paragraph("6. Orden recomendado de trabajo", S["H1Custom"]))
    story.append(numbers([
        "Semana 1: PR-01, PR-02, PR-03.",
        "Semana 2: PR-04, PR-05, PR-06, PR-07.",
        "Semana 3: PR-08, PR-09, PR-10.",
        "Semana 4: PR-11, PR-12, PR-13, PR-14.",
        "Semana 5: PR-15, PR-16, PR-17, PR-18.",
        "Semana 6: PR-19, PR-20, PR-21, PR-22.",
        "Semana 7: PR-23, PR-24, informe, evidencias, presentacion y demo.",
    ]))

    story.append(Paragraph("7. Detalle por PR con comandos", S["H1Custom"]))
    for pr, person, branch, scope, acceptance in PRS:
        block = [
            Paragraph(f"{pr} - {branch}", S["H2Custom"]),
            bullets([
                f"Responsable: {person}.",
                f"Alcance: {scope}",
                f"Criterio de aceptacion: {acceptance}",
            ]),
            code([
                "git checkout develop",
                "git pull origin develop",
                f"git checkout -b {branch}",
                "# implementar cambios del alcance",
                "php artisan test",
                "npm run build",
                "git add .",
                f"git commit -m \"feat: {scope[:55].lower()}\"",
                f"git push -u origin {branch}",
            ]),
            Spacer(1, 0.08 * inch),
        ]
        story.append(KeepTogether(block))

    story.append(PageBreak())
    story.append(Paragraph("8. Plantilla sugerida para cada Pull Request", S["H1Custom"]))
    story.append(code([
        "## Descripcion",
        "Resumen breve del cambio implementado.",
        "",
        "## Cambios realizados",
        "- Cambio 1",
        "- Cambio 2",
        "",
        "## Validaciones",
        "- php artisan test",
        "- npm run build",
        "",
        "## Evidencias",
        "- Capturas de pantalla",
        "- Ruta probada",
        "- Issue/Jira asociado",
        "",
        "## Checklist",
        "- [ ] Codigo revisado localmente",
        "- [ ] Migraciones probadas si aplica",
        "- [ ] No se mezclan tareas no relacionadas",
        "- [ ] PR apunta a develop",
    ]))

    story.append(Paragraph("9. Flujo de control de cambios con Jira", S["H1Custom"]))
    story.append(table([
        ["Estado", "Responsable", "Descripcion"],
        ["Backlog", "Comite / equipo", "Solicitud de cambio registrada y priorizada."],
        ["To Do", "Responsable asignado", "Cambio aprobado para implementacion."],
        ["In Progress", "Desarrollador", "Rama feature creada desde develop y trabajo en curso."],
        ["In Review", "Revisor", "Pull Request abierto, pruebas y evidencias adjuntas."],
        ["Approved", "Comite / revisor", "Cambio aceptado para merge a develop."],
        ["Done", "Equipo", "PR mergeado, rama eliminada y Jira actualizado."],
    ], [1.15 * inch, 1.65 * inch, 6.4 * inch]))

    story.append(Paragraph("10. Comandos para release final", S["H1Custom"]))
    story.append(code([
        "git checkout develop",
        "git pull origin develop",
        "git checkout -b release/v1.0.0",
        "php artisan test",
        "npm run build",
        "git add .",
        "git commit -m \"chore: preparar release v1.0.0\"",
        "git push -u origin release/v1.0.0",
        "# abrir PR release/v1.0.0 -> main",
        "# luego abrir PR main -> develop para sincronizar",
        "git tag -a v1.0.0 -m \"Version estable segundo parcial\"",
        "git push origin v1.0.0",
    ]))

    story.append(Paragraph("11. Checklist final del proyecto", S["H1Custom"]))
    story.append(bullets([
        "Repositorio en GitHub con ramas, commits y Pull Requests.",
        "Issues o tareas en Jira enlazadas a cada PR.",
        "Evidencia de revisiones entre integrantes.",
        "Migraciones y seeders necesarios para demo.",
        "Autenticacion y roles base funcionando.",
        "CRUDs principales funcionando: cooperativas, ciudades, buses, rutas, frecuencias y configuracion.",
        "Flujo de compra de boletos y pagos funcionando.",
        "Validacion de acceso de pasajeros funcionando.",
        "Informe con capturas del flujo Git, Jira y demo de la aplicacion.",
        "Presentacion grupal con demo de 20 a 30 minutos.",
    ]))

    story.append(Paragraph("12. Nota de alcance", S["H1Custom"]))
    story.append(p("La division propone PRs pequenos y revisables. Si alguna funcionalidad resulta demasiado grande, se recomienda dividirla en sub-PRs manteniendo el mismo prefijo de modulo. Por ejemplo, feature/venta-boletos-cliente-formulario y feature/venta-boletos-cliente-confirmacion."))

    def page_footer(canvas, doc_obj):
        canvas.saveState()
        canvas.setFont("Helvetica", 7)
        canvas.setFillColor(colors.HexColor("#64748B"))
        canvas.drawString(0.45 * inch, 0.25 * inch, "Manejo Buses - Plan de Pull Requests con Git Flow")
        canvas.drawRightString(10.55 * inch, 0.25 * inch, f"Pagina {doc_obj.page}")
        canvas.restoreState()

    doc.build(story, onFirstPage=page_footer, onLaterPages=page_footer)
    print(PDF_PATH)


if __name__ == "__main__":
    build_pdf()
