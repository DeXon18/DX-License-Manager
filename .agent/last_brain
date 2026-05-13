Rama activa: fix/nx-suite-transform. Bugs de transformación resueltos en NX Suite, StarCCM y Heeds.
Problema: str_starts_with y stripos detectaban VENDOR_STRING erróneamente. Falta de localhost en SERVER para temporales.
Solución:
1. NXSuiteService: preg_match estricto /^VENDOR\s+ugslmd\b/i y localhost unificado.
2. StarCcmService: preg_match estricto /^VENDOR\s+cdlmd\b/i y localhost unificado.
3. HeedsService: preg_match estricto /^VENDOR\s+RCTECH\b/i y localhost unificado.
Controladores actualizados para pasar el flag isTemporal.
Estado: Verificado con scripts de reproducción para los 3 servicios. Listo para merge a dev.
Próximo paso: Merge a dev.
Bloqueos: Ninguno.
Stack beta: running. Stack prod: running.
