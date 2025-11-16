-- Migration: create movimientos de cajas (Option B - normalized schema)
-- Uses utf8 and TEXT for broad MySQL compatibility (avoid utf8mb4/JSON syntax errors)

CREATE TABLE IF NOT EXISTS movimiento_caja (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATE NOT NULL,
  tipo ENUM('RECOJO','ENTREGA') NOT NULL,
  usuario_id INT NULL,
  observacion TEXT DEFAULT NULL,
  totales_json TEXT DEFAULT NULL,
  estado VARCHAR(32) DEFAULT 'CREADO',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_movimiento_fecha (fecha),
  INDEX idx_movimiento_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS movimiento_caja_linea (
  id INT AUTO_INCREMENT PRIMARY KEY,
  movimiento_id INT NOT NULL,
  nro_linea INT NOT NULL,
  persona_id INT NULL,
  obs VARCHAR(512) DEFAULT NULL,
  foto_flag TINYINT(1) DEFAULT 0,
  notad_flag TINYINT(1) DEFAULT 0,
  reccans_flag TINYINT(1) DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_mcl_movimiento (movimiento_id),
  INDEX idx_mcl_persona (persona_id),
  INDEX idx_mcl_nro (movimiento_id, nro_linea),
  CONSTRAINT fk_mcl_movimiento FOREIGN KEY (movimiento_id) REFERENCES movimiento_caja(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS movimiento_caja_cantidad (
  id INT AUTO_INCREMENT PRIMARY KEY,
  linea_id INT NOT NULL,
  tipo_caja_id INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_mcc_linea (linea_id),
  INDEX idx_mcc_tipocaja (tipo_caja_id),
  CONSTRAINT fk_mcc_linea FOREIGN KEY (linea_id) REFERENCES movimiento_caja_linea(id) ON DELETE CASCADE,
  CONSTRAINT fk_mcc_tipocaja FOREIGN KEY (tipo_caja_id) REFERENCES tipo_caja(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Optional attachments table
CREATE TABLE IF NOT EXISTS movimiento_caja_attachment (
  id INT AUTO_INCREMENT PRIMARY KEY,
  linea_id INT NULL,
  movimiento_id INT NULL,
  filename VARCHAR(255) NOT NULL,
  mime VARCHAR(64) DEFAULT NULL,
  uploaded_by INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mca_linea FOREIGN KEY (linea_id) REFERENCES movimiento_caja_linea(id) ON DELETE CASCADE,
  CONSTRAINT fk_mca_movimiento FOREIGN KEY (movimiento_id) REFERENCES movimiento_caja(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- End migration
