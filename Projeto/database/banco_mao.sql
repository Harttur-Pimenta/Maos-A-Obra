CREATE DATABASE IF NOT EXISTS banco_mao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE banco_mao;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  perfil ENUM('admin','engenheiro','tecnico','visualizador') DEFAULT 'tecnico',
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS obras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(200) NOT NULL,
  endereco TEXT,
  status ENUM('ativa','pausada','finalizada') DEFAULT 'ativa',
  data_inicio DATE,
  data_previsao DATE,
  data_fim DATE,
  responsavel_id INT,
  orcamento_total DECIMAL(15,2) DEFAULT 0.00,
  progresso_pct TINYINT UNSIGNED DEFAULT 0,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (responsavel_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS custos_obra (
  id INT AUTO_INCREMENT PRIMARY KEY,
  obra_id INT NOT NULL,
  usuario_id INT,
  descricao VARCHAR(255),
  tipo ENUM('material','servico','equipamento','outro') DEFAULT 'material',
  qtd_planejada DECIMAL(12,3) DEFAULT 0,
  qtd_realizada DECIMAL(12,3) DEFAULT 0,
  valor_planejado DECIMAL(15,2) DEFAULT 0.00,
  valor_realizado DECIMAL(15,2) DEFAULT 0.00,
  data_lancamento DATE,
  FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS ocorrencias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  obra_id INT NOT NULL,
  usuario_id INT,
  titulo VARCHAR(200) NOT NULL,
  descricao TEXT,
  categoria ENUM('segurança','qualidade','prazo','custo','clima','outro') DEFAULT 'outro',
  status ENUM('aberta','em_analise','resolvida') DEFAULT 'aberta',
  prioridade ENUM('baixa','media','alta','critica') DEFAULT 'media',
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS historico (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipo ENUM('ocorrencia','custo') NOT NULL,
  referencia_id INT NOT NULL,
  usuario_id INT,
  acao VARCHAR(100) NOT NULL,
  descricao TEXT,
  status_anterior VARCHAR(50),
  status_novo VARCHAR(50),
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

DROP TRIGGER IF EXISTS trg_historico_insert_custo;
DROP TRIGGER IF EXISTS trg_historico_update_custos;
DROP TRIGGER IF EXISTS trg_historico_insert_ocorrencia;
DROP TRIGGER IF EXISTS trg_historico_update_ocorrencias;

DELIMITER $$

CREATE TRIGGER trg_historico_insert_custo
AFTER INSERT ON custos_obra
FOR EACH ROW
BEGIN
    INSERT INTO historico (tipo, referencia_id, usuario_id, acao, descricao)
    VALUES (
        'custo',
        NEW.id,
        NEW.usuario_id,
        'Custo cadastrado',
        CONCAT('Novo custo lançado: ', COALESCE(NEW.descricao, ''), ' - R$ ', COALESCE(NEW.valor_realizado, 0))
    );
END$$

CREATE TRIGGER trg_historico_update_custos
AFTER UPDATE ON custos_obra
FOR EACH ROW
BEGIN
    INSERT INTO historico (tipo, referencia_id, usuario_id, acao, descricao)
    VALUES (
        'custo',
        NEW.id,
        NEW.usuario_id,
        'Custo atualizado',
        CONCAT('Alteração no custo: ', COALESCE(NEW.descricao, ''), ' - R$ ', COALESCE(NEW.valor_realizado, 0))
    );
END$$

CREATE TRIGGER trg_historico_insert_ocorrencia
AFTER INSERT ON ocorrencias
FOR EACH ROW
BEGIN
    INSERT INTO historico (tipo, referencia_id, usuario_id, acao, descricao, status_novo)
    VALUES (
        'ocorrencia',
        NEW.id,
        NEW.usuario_id,
        'Ocorrência criada',
        NEW.titulo,
        NEW.status
    );
END$$

CREATE TRIGGER trg_historico_update_ocorrencias
AFTER UPDATE ON ocorrencias
FOR EACH ROW
BEGIN
    INSERT INTO historico (tipo, referencia_id, usuario_id, acao, descricao, status_anterior, status_novo)
    VALUES (
        'ocorrencia',
        NEW.id,
        NEW.usuario_id,
        'Ocorrência atualizada',
        NEW.titulo,
        OLD.status,
        NEW.status
    );
END$$

DELIMITER ;

INSERT INTO usuarios (nome, email, senha_hash, perfil)
VALUES ('Administrador', 'admin@maoobra.com', MD5('123456'), 'admin')
ON DUPLICATE KEY UPDATE nome = VALUES(nome), perfil = VALUES(perfil);

INSERT INTO usuarios (nome, email, senha_hash, perfil)
VALUES ('João Engenheiro', 'engenheiro@maoobra.com', MD5('123456'), 'engenheiro')
ON DUPLICATE KEY UPDATE nome = VALUES(nome), perfil = VALUES(perfil);

-- Usuário de banco opcional para ambiente Codespaces/Linux.
-- No XAMPP normalmente basta usar root sem senha no configs/banco.php.
-- CREATE USER IF NOT EXISTS 'rootphp'@'localhost' IDENTIFIED BY '123456';
-- GRANT ALL PRIVILEGES ON banco_mao.* TO 'rootphp'@'localhost';
-- FLUSH PRIVILEGES;
