-- AgendaJá - Script de Banco de Dados
-- Execute este arquivo no painel MySQL da Hostgator (phpMyAdmin)
-- Banco: marc4901_agendaja

SET NAMES utf8mb4;
SET time_zone = '-03:00';

-- --------------------------------------------------------
-- Tabela de usuários do app (3 níveis)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`         VARCHAR(255) NOT NULL,
  `email`        VARCHAR(255) NOT NULL,
  `senha`        VARCHAR(255) NOT NULL,
  `tipo_usuario` ENUM('usuario_final','estabelecimento','funcionario') NOT NULL DEFAULT 'usuario_final',
  `empresa_id`   INT UNSIGNED NULL DEFAULT NULL COMMENT 'Para funcionarios: ID do estabelecimento que pertencem',
  `ativo`        TINYINT(1) NOT NULL DEFAULT 1,
  `criado_em`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `empresa_id` (`empresa_id`),
  KEY `tipo_usuario` (`tipo_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de administradores do painel PHP
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `login`     VARCHAR(100) NOT NULL,
  `senha`     VARCHAR(255) NOT NULL,
  `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Admin padrão: login=admin senha=admin
-- (A senha abaixo é o hash de 'admin' gerado com password_hash do PHP)
-- IMPORTANTE: Troque a senha após o primeiro acesso!
-- --------------------------------------------------------
INSERT IGNORE INTO `admins` (`login`, `senha`)
VALUES (
  'admin',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
);

-- --------------------------------------------------------
-- Notas:
-- * empresa_id em usuarios aponta para outro registro de usuarios (tipo=estabelecimento)
-- * Funcionarios tem empresa_id preenchido
-- * Usuarios finais e estabelecimentos tem empresa_id NULL
-- --------------------------------------------------------
