-- V2: Popular tabelas com dados iniciais

-- Usuário admin (senha: admin123)
INSERT INTO usuario (nome, login, senha, situacao) VALUES
('Administrador', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ativo');

-- 10 receitas iniciais
INSERT INTO receita (nome, descricao, data_registro, custo, tipo_receita) VALUES
('Brigadeiro',        'Brigadeiro tradicional de chocolate com granulado',           '2026-01-10', 2.50,  'doce'),
('Coxinha',           'Coxinha de frango com catupiry crocante',                     '2026-01-12', 4.00,  'salgada'),
('Bolo de Cenoura',   'Bolo de cenoura fofinho com cobertura de chocolate',          '2026-01-15', 18.00, 'doce'),
('Esfiha',            'Esfiha aberta de carne moída temperada',                      '2026-01-18', 3.50,  'salgada'),
('Pudim de Leite',    'Pudim de leite condensado com calda de caramelo',             '2026-01-20', 12.00, 'doce'),
('Pastel de Queijo',  'Pastel frito crocante recheado com queijo derretido',         '2026-01-22', 3.00,  'salgada'),
('Quindim',           'Quindim amarelo brilhante feito com coco e gemas',            '2026-01-25', 1.80,  'doce'),
('Pão de Queijo',     'Pão de queijo mineiro macio por dentro e crocante por fora',  '2026-02-01', 1.50,  'salgada'),
('Mousse de Maracujá','Mousse leve e cremosa de maracujá com calda',                 '2026-02-05', 8.00,  'doce'),
('Risole de Camarão', 'Risole empanado recheado com camarão ao molho branco',        '2026-02-10', 5.50,  'salgada');
