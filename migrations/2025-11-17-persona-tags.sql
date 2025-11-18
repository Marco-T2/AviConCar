-- Migration: add tags and persona_tags tables
CREATE TABLE IF NOT EXISTS tb_tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tag VARCHAR(64) NOT NULL UNIQUE,
  descripcion VARCHAR(255) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_persona_tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  persona_id INT NOT NULL,
  tag_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(persona_id, tag_id),
  INDEX idx_ptag_persona (persona_id),
  INDEX idx_ptag_tag (tag_id),
  CONSTRAINT fk_ptag_persona FOREIGN KEY (persona_id) REFERENCES tb_personas(id_persona) ON DELETE CASCADE,
  CONSTRAINT fk_ptag_tag FOREIGN KEY (tag_id) REFERENCES tb_tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: Run this migration on your database (via mysql client) to create the tables.
