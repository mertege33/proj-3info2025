# UI Guidelines - Marcela (Resumo)

## Cores principais
- Accent: #1CECE7
- Background dark: rgba(0,0,0,0.45)
- Card background: rgba(10,10,10,0.55)

## Tipografia
- Fonte: Poppins (weights 400,600,700,800)
- Tamanhos: H1 grande para o título do hero (usar clamp para responsividade)

## Componentes
- Botões: `.btn.primary` e `.btn.ghost`
- Card de módulo: `.module-card`
- Badges: `.badge.dev` / `.badge.ready`

## Recomendações para as equipes
- Crie páginas novas seguindo `module-template.html`.
- Mantenha classes padrão (`module-card`, `mc-title`, `mc-desc`) quando adicionar entradas ao menu.
- Use SVGs leves e evitar imagens pesadas — prefira vetores e ícones.

## Acessibilidade
- Fornecer `aria-label` em links e botões importantes.
- Garantir contraste de texto em relação ao fundo (WCAG AA mínimo).
- Navegabilidade por teclado: foco visível e ordem lógica.

## Como integrar ao repositório
- Cada equipe cria uma pasta `modulo-nome/` com `index.html`, assets, e um README com dependências.
- Atualizar o `menu.html` com o href apontando para a página do módulo quando pronta.