#Prueba 1
# from flask import Flask

# app = Flask(__name__)

# @app.route("/")
# def home():
#     return "Modulo Cartera funcionando 🚀"

# if __name__ == "__main__":
#     app.run(host="127.0.0.1", port=5050, debug=True)

#Prueba 2
# from flask import Flask, jsonify
# from db import get_connection

# app = Flask(__name__)

# @app.route("/")
# def home():
#     return "Modulo Cartera funcionando 🚀"

# @app.route("/test-db")
# def test_db():
#     try:
#         conn = get_connection()
#         cursor = conn.cursor()
#         cursor.execute("SELECT NOW();")
#         result = cursor.fetchone()
#         cursor.close()
#         conn.close()

#         return jsonify({
#             "status": "success",
#             "server_time": result[0]
#         })
#     except Exception as e:
#         return jsonify({
#             "status": "error",
#             "message": str(e)
#         })

# if __name__ == "__main__":
#     app.run(host="127.0.0.1", port=5050, debug=True)

#Prueba 3
from flask import Flask, request, jsonify, render_template
from db import get_connection

app = Flask(__name__)

# ==========================================================
# 🔹 INDEX
# ==========================================================

@app.route("/")
def index():
    return render_template("index.html")


# ==========================================================
# 🔹 VISTA PRINCIPAL CARTERA
# ==========================================================

@app.route("/cartera")
def cartera_view():
    return render_template("cartera.html")


# ==========================================================
# 🔹 MODULOS DE CARGUE
# ==========================================================

@app.route("/upload/migrantes")
def upload_migrantes():
    return render_template("upload_migrantes.html")

@app.route("/upload/urgencias")
def upload_urgencias():
    return render_template("upload_urgencias.html")

@app.route("/upload/nopos")
def upload_nopos():
    return render_template("upload_nopos.html")


@app.route("/cartera/acta")
def cartera_por_acta():
    try:
        nit = request.args.get("nit")
        year = request.args.get("year")
        acta = request.args.get("acta")
        no_factura = request.args.get("no_factura")

        # ✅ Validación correcta
        if not nit and not acta and not no_factura:
            return jsonify({
                "status": "error",
                "message": "Debe ingresar al menos un filtro"
            }), 400

        conn = get_connection()
        cursor = conn.cursor(dictionary=True)

        query = """
            SELECT 
                fm.acta, 
                SUM(fm.VALOR_FACTURA) AS SUMA_VALOR_FACTURA, 
                SUM(fm.VALOR_GLOSADO) AS SUMA_DE_VALOR_GLOSADO, 
                SUM(fm.VALOR_RECONOCIDO) AS SUMA_DE_VALOR_RECONOCIDO, 
                SUM(fm.VALOR_PAGADO) AS SUMA_DE_VALOR_PAGADO,
                SUM(fm.VALOR_SALDO) AS SUMA_DE_VALOR_SALDO,
                SUM(fm.PASIVO_CONTINGENTE) AS SUMA_DE_PASIVO_CONTINGENTE,
                COUNT(fm.ACTA) AS CUENTA_No_FACTURA
            FROM facturas_mig fm
            WHERE 1=1
        """

        params = []

        # 🔹 Filtro NIT (solo si existe)
        if nit:
            query += " AND fm.nit = %s"
            params.append(nit)

        # 🔹 Filtro ACTA (solo si existe)
        if acta:
            query += " AND fm.acta = %s"
            params.append(acta)

        # 🔹 Filtro Año
        if year and year != "*":
            query += " AND YEAR(fm.fecha_factura) < %s"
            params.append(year)

        # 🔹 Filtro NO_FACTURA
        if no_factura:
            query += " AND fm.NO_FACTURA = %s"
            params.append(no_factura)

        query += " GROUP BY fm.acta"

        cursor.execute(query, tuple(params))
        result = cursor.fetchall()

        cursor.close()
        conn.close()

        return jsonify({
            "status": "success",
            "data": result
        })

    except Exception as e:
        return jsonify({
            "status": "error",
            "message": str(e)
        }), 500

# ==========================================================
# 🔹 RESUMEN GENERAL (SIN FILTROS)
# ==========================================================

@app.route("/cartera/resumen")
def cartera_resumen():
    try:
        conn = get_connection()
        cursor = conn.cursor(dictionary=True)

        query = """
            SELECT 
                estado,
                COALESCE(SUM(valor),0) AS total_valor,
                COALESCE(SUM(cantidad),0) AS total_cantidad
            FROM (
                SELECT 
                    'EN PROCESO DE CONCILIACION' AS estado,
                    SUM(CASE WHEN acta = 'EN PROCESO DE CONCILIACION' THEN VALOR_FACTURA ELSE 0 END) AS valor,
                    COUNT(CASE WHEN acta = 'EN PROCESO DE CONCILIACION' THEN 1 END) AS cantidad
                FROM facturas_mig

                UNION ALL

                SELECT 
                    'EN PROCESO DE AUDITORIA',
                    SUM(CASE WHEN acta = 'EN PROCESO DE AUDITORIA' THEN VALOR_FACTURA ELSE 0 END),
                    COUNT(CASE WHEN acta = 'EN PROCESO DE AUDITORIA' THEN 1 END)
                FROM facturas_mig

                UNION ALL

                SELECT 
                    'SIN ASIGNAR',
                    SUM(CASE WHEN acta = 'SIN ASIGNAR' THEN VALOR_FACTURA ELSE 0 END),
                    COUNT(CASE WHEN acta = 'SIN ASIGNAR' THEN 1 END)
                FROM facturas_mig
            ) t
            GROUP BY estado
        """

        cursor.execute(query)
        result = cursor.fetchall()

        cursor.close()
        conn.close()

        return jsonify({
            "status": "success",
            "data": result
        })

    except Exception as e:
        return jsonify({
            "status": "error",
            "message": str(e)
        }), 500


# ==========================================================
# 🔹 AUTOCOMPLETE NIT (CON NOMBRE IPS)
# ==========================================================

@app.route("/autocomplete_nit")
def autocomplete_nit():
    term = request.args.get("term", "")

    conn = get_connection()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("""
        SELECT DISTINCT 
            fm.NIT AS nit,
            fm.IPS AS nombre
        FROM facturas_mig fm
        WHERE fm.NIT LIKE %s
        LIMIT 10
    """, (f"{term}%",))

    resultados = cursor.fetchall()

    cursor.close()
    conn.close()

    return jsonify(resultados)


# ==========================================================
# 🔹 AUTOCOMPLETE ACTA
# ==========================================================

@app.route("/autocomplete_acta")
def autocomplete_acta():
    term = request.args.get("term", "")

    conn = get_connection()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("""
        SELECT DISTINCT fm.acta AS acta
        FROM facturas_mig fm
        WHERE fm.acta LIKE %s
        LIMIT 10
    """, (f"{term}%",))

    resultados = cursor.fetchall()

    cursor.close()
    conn.close()

    return jsonify(resultados)

# ==========================================================
# 🔹 AUTOCOMPLETE NO_FACTURA
# ==========================================================

@app.route("/autocomplete_factura")
def autocomplete_factura():
    term = request.args.get("term", "")

    conn = get_connection()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("""
        SELECT DISTINCT fm.NO_FACTURA AS no_factura
        FROM facturas_mig fm
        WHERE fm.NO_FACTURA LIKE %s
        LIMIT 10
    """, (f"{term}%",))

    resultados = cursor.fetchall()

    cursor.close()
    conn.close()

    return jsonify(resultados)


# ==========================================================
# 🔹 DETALLE DE FACTURAS POR ACTA
# ==========================================================

@app.route("/cartera/detalle")
def cartera_detalle():
    try:
        acta = request.args.get("acta")

        if not acta:
            return jsonify({
                "status": "error",
                "message": "Debe enviar ACTA"
            }), 400

        conn = get_connection()
        cursor = conn.cursor(dictionary=True)

        query = """
            SELECT 
                fm.NO_FACTURA,
                fm.NIT,
                fm.IPS,
                fm.VALOR_FACTURA,
                fm.VALOR_GLOSADO,
                fm.VALOR_RECONOCIDO,
                fm.VALOR_PAGADO,
                fm.VALOR_SALDO
            FROM facturas_mig fm
            WHERE fm.acta = %s
        """

        cursor.execute(query, (acta,))
        result = cursor.fetchall()

        cursor.close()
        conn.close()

        return jsonify({
            "status": "success",
            "data": result
        })

    except Exception as e:
        return jsonify({
            "status": "error",
            "message": str(e)
        }), 500

        
# ==========================================================
# 🔹 RUN LOCAL
# ==========================================================

if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5050, debug=True)

