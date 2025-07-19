# 🚢 Sistema de Gestión para Astillero Naval - Marina

Bienvenido al proyecto **Marina-Astillero**, un sistema web diseñado para optimizar y digitalizar la gestión operativa de un astillero naval. Este proyecto permite llevar el control de buques, órdenes de trabajo, materiales, tickets y personal de manera organizada, rápida y centralizada.

---

## 🧭 Objetivo General

Desarrollar una plataforma web dinámica que permita administrar eficazmente el flujo de trabajo en un astillero naval, desde el ingreso de buques hasta su liberación, incluyendo documentación, mantenimiento, control de materiales y asignación de personal.

---

## 🛠️ Tecnologías Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP (versión 8.2)
- **Base de Datos**: MySQL / MariaDB
- **Herramientas adicionales**:
  - phpMyAdmin
  - XAMPP (entorno local)
  - Bootstrap (opcional)
  - Iconografía SVG

---

## 🧩 Módulos Principales

### 1. **Gestión de Buques**
- Registro de buques (nombre, matrícula, tipo, estado)
- Documentación asociada (pendiente la función de PDF)
- Seguimiento de entrada/salida

### 2. **Órdenes de Trabajo**
- Generación y seguimiento de órdenes por prioridad y estado
- Asignación de personal técnico
- Carga de materiales requeridos

### 3. **Tickets**
- Generación automática de tickets por buque
- Validación/rechazo con motivo
- Control de flujo antes de liberar buques

### 4. **Asignaciones de Personal**
- Asignación por especialidad (marino, teniente, contramaestre, etc.)
- Estado de actividad: asignado, en progreso, completado
- Registro de fechas y responsabilidades

### 5. **Bitácora del Sistema**
- Historial de acciones por usuarios
- Tabla de auditoría básica (sin IP ni firmas digitales, por ahora)

### 6. **Gestión de Materiales**
- Alta de herramientas, repuestos, consumibles y equipos
- Estado del inventario: disponible, agotado, reservado
- Control de uso por orden de trabajo

---

## 🔐 Roles del Sistema

| Rol             | Funcionalidad clave                                 |
|----------------|------------------------------------------------------|
| **Admin**       | Control total, administración del sistema           |
| **Mando**       | Validación y seguimiento de órdenes                 |
| **Infraestructura** | Supervisión y materiales                       |
| **Taller**      | Ejecución de órdenes y uso de recursos              |
| **Entrada**     | Registro de buques y generación de tickets          |

---

## 🤓 Créditos

Desarrollado por **Luis Cruz**  
Proyecto escolar con enfoque en logística naval y sistemas de información.  
Inspirado por las necesidades reales de administración en astilleros navales.
