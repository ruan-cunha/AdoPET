-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/07/2025 às 19:07
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `adopet`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `animais`
--

CREATE TABLE `animais` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `especie` varchar(50) NOT NULL,
  `raca` varchar(100) DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `genero` enum('Macho','Fêmea') NOT NULL,
  `porte` enum('Pequeno','Medio','Grande') NOT NULL,
  `castrado` tinyint(1) DEFAULT NULL,
  `vacinado` tinyint(1) DEFAULT NULL,
  `vermifugado` tinyint(1) DEFAULT NULL,
  `descricao` text NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `disponivel` tinyint(1) DEFAULT 1,
  `id_usuario` int(11) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `animais`
--
-- --------------------------------------------------------

--
-- Estrutura para tabela `interesses_adocao`
--

CREATE TABLE `interesses_adocao` (
  `id` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `id_interessado` int(11) NOT NULL,
  `status` enum('Pendente','Em Análise','Aprovado','Rejeitado','Adotado') DEFAULT 'Pendente',
  `mensagem_interessado` text DEFAULT NULL,
  `mensagem_doador` text DEFAULT NULL,
  `data_interesse` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `interesses_adocao`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_usuario` enum('ONG','Pessoa Fisica') NOT NULL,
  `documento` varchar(20) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `animais`
--
ALTER TABLE `animais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `interesses_adocao`
--
ALTER TABLE `interesses_adocao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_animal` (`id_animal`),
  ADD KEY `id_interessado` (`id_interessado`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `animais`
--
ALTER TABLE `animais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `interesses_adocao`
--
ALTER TABLE `interesses_adocao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `animais`
--
ALTER TABLE `animais`
  ADD CONSTRAINT `animais_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `interesses_adocao`
--
ALTER TABLE `interesses_adocao`
  ADD CONSTRAINT `interesses_adocao_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animais` (`id`),
  ADD CONSTRAINT `interesses_adocao_ibfk_2` FOREIGN KEY (`id_interessado`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
