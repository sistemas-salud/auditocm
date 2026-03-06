# auditocm
#
# PASOS PARA INICIAR LA APLICACION
# 
# 1. ABRIR EL TUNEL
# 1.1 gcloud auth login
# 1.2 gcloud compute start-iap-tunnel op-srv-app-apprethus-prd1-ubu22-14 3306 --local-host-port=localhost:3306 --zone=us-east1-d --project co-apprethus-prd
# 
# 2. ACCEDER POR CDM O POWERSHELL A LA RUTA auditocm
# 2.1 cd .\Desktop\auditocm\apps\cartera-service
# 
# 3. INICIAR EL AMBIENTE VIRTUAL
# 3.1 venv\Scripts\activate
# 
# 4. ACTIVAR LA APP
# 4.1 python app.py
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 
en este momento se necesita incluir un campo debajo del formulario de consulta de cartera, debajo del NIT y AÑo, donde salga el nombre de la IPS que corresponde del NIT, ya que se puede prestar para confusion en algun momento, que funcione que una ve 