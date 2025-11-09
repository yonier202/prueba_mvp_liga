
# 🧠 DECISIONES.md

Aceptar el pull request que contienen las pruebas unitarias, environments separados y endpoint + vista en el front para crear partidos.

## 📋 Contexto del Proyecto
El sistema de **Mini Liga** consiste en tres componentes:
- **Backend (API Laravel):** provee los servicios RESTful para gestionar equipos, partidos y clasificación.  
- **Frontend Web (Angular):** panel de administración para crear equipos y ver la tabla de posiciones.  
- **App Móvil (Ionic + Angular):** interfaz para jugadores o gestores que reportan los resultados de los partidos.

El objetivo del MVP es ofrecer una base sólida para expandir hacia un sistema completo de gestión de ligas con autenticación, roles y más estadísticas.

---

## ⚙️ Decisiones Técnicas

### 1. Frameworks y tecnologías
| Capa | Tecnología | Justificación |
|------|-------------|----------------|
| Backend | Laravel 11 (PHP) | Framework robusto, con ORM Eloquent y Sanctum para escalabilidad. |
| Web | Angular 18 | Ideal para SPA administrativas y escalabilidad modular. |
| Móvil | Ionic 8 + Angular | Permite desarrollar una sola app para Android/iOS usando el mismo stack Angular. |
| BD | MySQL / MariaDB | Compatibilidad con Laravel y facilidad de despliegue. |

---

### 2. Diseño del Backend
#### Endpoints principales:
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/teams` | Lista todos los equipos |
| POST | `/api/teams` | Crea un nuevo equipo |
| GET | `/api/matches` | Lista los partidos |
| POST | `/api/matches/{id}/result` | Reporta el resultado del partido |
| GET | `/api/standings` | Retorna la clasificación general |

**Decisión:**  
Se mantuvo el backend totalmente orientado a servicios (API RESTful) sin vistas Blade.  
Esto permite reutilizar la lógica para web y móvil sin duplicar código.

**Trade-off:**  
No se usa autenticación en esta fase (para simplificar el MVP).  
Más adelante se podrá integrar Sanctum o Passport para autenticación por tokens.

---

### 3. Estructura de relaciones
#### Modelos:
- **Team** (tiene muchos `matches` como local y visitante)
- **Match** (pertenece a dos `teams` y almacena resultados)

Relaciones en Eloquent:
```php
// Team.php
public function homeMatches() {
    return $this->hasMany(Match::class, 'home_team_id');
}
public function awayMatches() {
    return $this->hasMany(Match::class, 'away_team_id');
}

// Match.php
public function homeTeam() {
    return $this->belongsTo(Team::class, 'home_team_id');
}
public function awayTeam() {
    return $this->belongsTo(Team::class, 'away_team_id');
}
```

**Decisión:**  
Se optó por una estructura simple con dos relaciones directas, lo cual facilita los cálculos de standings.

**Trade-off:**  
La tabla de standings no se persiste, se calcula dinámicamente desde los resultados → más carga de procesamiento, pero datos siempre actualizados.

---

### 4. Frontend Web (Angular)
- Módulo **Teams** → formulario + lista de equipos.  
- Módulo **Standings** → tabla dinámica con posiciones, puntos, PJ, PG, PE, PP.

**Decisión:**  
Se usa Angular Material para formularios y tablas reactivas.

**Trade-off:**  
Angular requiere mayor configuración inicial comparado con React o Vue, pero su estructura modular es ideal para sistemas administrativos.

---

### 5. App Móvil (Ionic + Angular)
- Página **Matches** → lista de partidos pendientes (`GET /api/matches`).  
- Página **Report Result** → formulario que usa `POST /api/matches/{id}/result`.

**Decisión:**  
Se aprovecha el stack Angular para compartir parte del código y lógica de servicios con la web.

**Trade-off:**  
El tamaño inicial de la app es mayor (por Angular), pero facilita el mantenimiento al tener un mismo lenguaje y estructura.

---

## 🧩 Trade-offs Generales

| Decisión | Beneficio | Costo o Limitación |
|-----------|------------|--------------------|
| API sin autenticación | Simplicidad para MVP | No hay control de acceso |
| Standings calculados en runtime | Datos siempre actualizados | Más consumo de CPU si crece la data |
| Stack unificado (Angular + Ionic) | Reutilización de código | Bundle inicial más pesado |
| Laravel como backend REST | Facilidad de desarrollo | Requiere hosting con PHP |
| MySQL relacional | Integración sencilla | Escalabilidad limitada frente a NoSQL |

---

## 🚀 Próximos Pasos

### Fase 2 – Mejoras técnicas
1. Agregar **autenticación con Sanctum** (usuarios admin / jugadores).  
2. Validar datos y manejar errores con mensajes personalizados.  
3. Implementar **paginación y búsqueda** en `/api/teams`.  
4. Desplegar API y web en servidores separados (ej. Vercel + Render + Railway).  
5. Crear un formulario para crear los partidos.

### Fase 3 – Funcionalidades nuevas
1. Historial de resultados por equipo.  
2. Gráficos en standings (goles a favor/en contra).  
3. Subida de imágenes (logo del equipo).  
4. Módulo de **notificaciones push** en Ionic al actualizar resultados.  
5. Panel administrativo con autenticación JWT.

---

## ✅ Conclusión

Este MVP permite:
- Crear equipos y visualizar la clasificación.  
- Consultar y reportar resultados desde la app móvil.  
- Compartir la misma API RESTful entre web y móvil.

El enfoque basado en **servicios modulares** y un **stack unificado (Laravel + Angular + Ionic)** asegura escalabilidad y mantenimiento futuro.
