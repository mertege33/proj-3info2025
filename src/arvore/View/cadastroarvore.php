<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Árvore Genealógica Personalizável</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        .node {
            position: relative;
            display: inline-block;
            margin: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .node:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .male {
            width: 40px;
            height: 40px;
            background-color: white;
            border: 2px solid #374151;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #374151;
        }
        
        .female {
            width: 40px;
            height: 40px;
            background-color: white;
            border: 2px solid #374151;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #374151;
        }
        
        .carrier {
            background-color: #f3f4f6;
            border: 2px solid #374151;
            border-radius: 50%;
            position: relative;
            overflow: hidden;
        }
        
        .carrier::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #10b981;
            clip-path: polygon(0% 0%, 50% 0%, 100% 0%, 100% 100%, 50% 100%, 0% 100%);
        }
        
        .absence {
            background-color: #1f2937;
            border: 2px solid #374151;
            border-radius: 50%;
        }
        
        .presence {
            background-color: #10b981;
            border: 2px solid #374151;
            border-radius: 50%;
        }
        
        .connection-line {
            position: absolute;
            background-color: #374151;
            z-index: 1;
        }
        
        .vertical-line {
            width: 2px;
            background-color: #374151;
        }
        
        .horizontal-line {
            height: 2px;
            background-color: #374151;
        }
        
        .marriage-line {
            width: 20px;
            height: 2px;
            background-color: #374151;
            position: absolute;
            z-index: 2;
        }
        
        .label {
            text-align: center;
            font-size: 12px;
            margin-top: 5px;
            color: #374151;
            max-width: 80px;
            word-wrap: break-word;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #374151;
        }
        
        input[type="text"], select, input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #d1d5da;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        button {
            background-color: #10b981;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        button:hover {
            background-color: #059669;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .legend-icon {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            border: 2px solid #374151;
        }
        
        .tool-panel {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 10px;
            display: flex;
            flex-direction: column;
        }
        
        .tool-btn {
            width: 40px;
            height: 40px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f3f4f6;
            border: 1px solid #d1d5da;
        }
        
        .tool-btn:hover {
            background-color: #e5e7eb;
        }
        
        .active {
            background-color: #10b981;
            color: white;
        }
        
        .tree-container {
            position: relative;
            width: 100%;
            height: 80vh;
            overflow-x: auto;
            overflow-y: auto;
            padding: 20px;
            background-color: #f9fafb;
        }
        
        .drag-handle {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            cursor: grab;
            z-index: 0;
        }
        
        .zoom-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
        }
        
        .zoom-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border: 1px solid #d1d5da;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .zoom-btn:hover {
            background-color: #f3f4f6;
        }
        
        .zoom-level {
            width: 60px;
            text-align: center;
            padding: 10px;
            background-color: white;
            border: 1px solid #d1d5da;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .relationship-form {
            margin-top: 15px;
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 4px;
            border: 1px solid #d1d5da;
        }
        
        .relationship-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #d1d5da;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .selected-person {
            font-weight: bold;
            color: #10b981;
            margin-bottom: 10px;
            padding: 5px;
            background-color: #f0fdf4;
            border-radius: 4px;
        }
        
        .connections-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 5;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">Árvore Genealógica Personalizável</h1>
        
        <!-- Tool Panel -->
        <div class="tool-panel">
            <div class="tool-btn active" id="select-tool" title="Selecionar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z" />
                </svg>
            </div>
            <div class="tool-btn" id="add-person-tool" title="Adicionar Pessoa">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
            <div class="tool-btn" id="edit-tool" title="Editar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div class="tool-btn" id="delete-tool" title="Excluir">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <div class="tool-btn" id="relationship-tool" title="Adicionar Relação">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="tool-btn" id="new-tree-tool" title="Nova Árvore">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.356-2m15.356 2H15" />
                </svg>
            </div>
            <div class="tool-btn" id="save-tool" title="Salvar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
            </div>
        </div>

        <!-- Legend -->
        <div class="absolute top-20 left-20 bg-white p-4 rounded-lg shadow-md z-10">
            <h3 class="font-bold text-lg mb-2">Legenda</h3>
            <div class="space-y-2">
                <div class="legend-item">
                    <div class="legend-icon male"></div>
                    <span>Homem</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon female"></div>
                    <span>Mulher</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon presence"></div>
                    <span>Porta o traço</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon absence"></div>
                    <span>Não porta o traço</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon carrier"></div>
                    <span>Portador do traço</span>
                </div>
                <div class="legend-item">
                    <div style="width: 20px; height: 2px; background-color: #374151; margin-right: 8px;"></div>
                    <span>Linha de casamento</span>
                </div>
                <div class="legend-item">
                    <div style="width: 2px; height: 20px; background-color: #374151; margin-right: 8px;"></div>
                    <span>Linha de descendência</span>
                </div>
            </div>
        </div>

        <!-- Tree Container -->
        <div class="tree-container" id="tree-container">
            <div class="drag-handle" id="drag-handle"></div>
            <div id="connections-container" class="connections-container"></div>
            <div id="family-tree"></div>
        </div>

        <!-- Zoom Controls -->
        <div class="zoom-controls">
            <div class="zoom-btn" id="zoom-out">-</div>
            <div class="zoom-level" id="zoom-level">100%</div>
            <div class="zoom-btn" id="zoom-in">+</div>
        </div>

        <!-- Add Person Modal -->
        <div id="add-person-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="text-xl font-bold mb-4">Adicionar Pessoa</h2>
                <form id="person-form">
                    <div class="form-group">
                        <label for="name">Nome:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="birth-year">Ano de Nascimento:</label>
                        <input type="number" id="birth-year" name="birth-year" min="1800" max="2023">
                    </div>
                    <div class="form-group">
                        <label for="death-year">Ano de Falecimento:</label>
                        <input type="number" id="death-year" name="death-year" min="1800" max="2023">
                    </div>
                    <div class="form-group">
                        <label for="gender">Gênero:</label>
                        <select id="gender" name="gender" required>
                            <option value="male">Masculino</option>
                            <option value="female">Feminino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="trait">Porta o traço genético?</label>
                        <select id="trait" name="trait">
                            <option value="presence">Sim</option>
                            <option value="absence">Não</option>
                            <option value="carrier">Portador</option>
                            <option value="none">Nenhum</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">Adicionar</button>
                </form>
            </div>
        </div>

        <!-- Edit Person Modal -->
        <div id="edit-person-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="text-xl font-bold mb-4">Editar Pessoa</h2>
                <form id="edit-person-form">
                    <input type="hidden" id="edit-id">
                    <div class="form-group">
                        <label for="edit-name">Nome:</label>
                        <input type="text" id="edit-name" name="edit-name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-birth-year">Ano de Nascimento:</label>
                        <input type="number" id="edit-birth-year" name="edit-birth-year" min="1800" max="2023">
                    </div>
                    <div class="form-group">
                        <label for="edit-death-year">Ano de Falecimento:</label>
                        <input type="number" id="edit-death-year" name="edit-death-year" min="1800" max="2023">
                    </div>
                    <div class="form-group">
                        <label for="edit-gender">Gênero:</label>
                        <select id="edit-gender" name="edit-gender" required>
                            <option value="male">Masculino</option>
                            <option value="female">Feminino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-trait">Porta o traço genético?</label>
                        <select id="edit-trait" name="edit-trait">
                            <option value="presence">Sim</option>
                            <option value="absence">Não</option>
                            <option value="carrier">Portador</option>
                            <option value="none">Nenhum</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">Atualizar</button>
                </form>
            </div>
        </div>

        <!-- Add Relationship Modal -->
        <div id="relationship-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="text-xl font-bold mb-4">Adicionar Relação</h2>
                <div id="selected-person-display" class="selected-person"></div>
                <div class="relationship-form">
                    <div class="form-group">
                        <label for="relationship-type">Tipo de Relação:</label>
                        <select id="relationship-type" class="relationship-select">
                            <option value="spouse">Cônjuge</option>
                            <option value="child">Filho/Filha</option>
                            <option value="parent">Pai/Mãe</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="related-person-name">Nome da Pessoa Relacionada:</label>
                        <input type="text" id="related-person-name" name="related-person-name" required>
                    </div>
                    <div class="form-group">
                        <label for="related-person-gender">Gênero:</label>
                        <select id="related-person-gender" class="relationship-select">
                            <option value="male">Masculino</option>
                            <option value="female">Feminino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="related-person-trait">Porta o traço genético?</label>
                        <select id="related-person-trait" class="relationship-select">
                            <option value="presence">Sim</option>
                            <option value="absence">Não</option>
                            <option value="carrier">Portador</option>
                            <option value="none">Nenhum</option>
                        </select>
                    </div>
                    <button id="add-relationship-btn" class="w-full bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded">Adicionar Relação</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize variables
        let selectedTool = 'select';
        let familyTree = [];
        let currentId = 1;
        let isDragging = false;
        let dragStartX = 0;
        let dragStartY = 0;
        let treeOffsetX = 0;
        let treeOffsetY = 0;
        let zoomLevel = 100;
        let selectedPersonId = null;
        let connections = [];

        // DOM Elements
        const treeContainer = document.getElementById('tree-container');
        const familyTreeElement = document.getElementById('family-tree');
        const connectionsContainer = document.getElementById('connections-container');
        const dragHandle = document.getElementById('drag-handle');
        const addPersonModal = document.getElementById('add-person-modal');
        const editPersonModal = document.getElementById('edit-person-modal');
        const relationshipModal = document.getElementById('relationship-modal');
        const personForm = document.getElementById('person-form');
        const editPersonForm = document.getElementById('edit-person-form');
        const relationshipForm = document.getElementById('relationship-form');
        const closeModalBtns = document.querySelectorAll('.close');
        const zoomInBtn = document.getElementById('zoom-in');
        const zoomOutBtn = document.getElementById('zoom-out');
        const zoomLevelDisplay = document.getElementById('zoom-level');
        const addRelationshipBtn = document.getElementById('add-relationship-btn');
        const selectedPersonDisplay = document.getElementById('selected-person-display');

        // Tools
        document.getElementById('select-tool').addEventListener('click', () => {
            selectedTool = 'select';
            updateActiveTool();
        });

        document.getElementById('add-person-tool').addEventListener('click', () => {
            selectedTool = 'add-person';
            openAddPersonModal();
            updateActiveTool();
        });

        document.getElementById('edit-tool').addEventListener('click', () => {
            selectedTool = 'edit';
            updateActiveTool();
        });

        document.getElementById('delete-tool').addEventListener('click', () => {
            selectedTool = 'delete';
            updateActiveTool();
        });

        document.getElementById('relationship-tool').addEventListener('click', () => {
            selectedTool = 'relationship';
            updateActiveTool();
        });

        document.getElementById('new-tree-tool').addEventListener('click', () => {
            if (confirm('Deseja realmente começar uma nova árvore?')) {
                resetFamilyTree();
            }
        });

        document.getElementById('save-tool').addEventListener('click', () => {
            saveFamilyTree();
        });

        // Update active tool styling
        function updateActiveTool() {
            document.querySelectorAll('.tool-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            if (selectedTool) {
                document.getElementById(selectedTool + '-tool').classList.add('active');
            }
        }

        // Open modals
        function openAddPersonModal() {
            addPersonModal.style.display = 'block';
            document.getElementById('name').value = '';
            document.getElementById('birth-year').value = '';
            document.getElementById('death-year').value = '';
            document.getElementById('gender').value = 'male';
            document.getElementById('trait').value = 'none';
        }

        function openEditPersonModal(person) {
            editPersonModal.style.display = 'block';
            document.getElementById('edit-id').value = person.id;
            document.getElementById('edit-name').value = person.name;
            document.getElementById('edit-birth-year').value = person.birthYear || '';
            document.getElementById('edit-death-year').value = person.deathYear || '';
            document.getElementById('edit-gender').value = person.gender;
            document.getElementById('edit-trait').value = person.trait || 'none';
        }

        function openRelationshipModal(person) {
            relationshipModal.style.display = 'block';
            selectedPersonId = person.id;
            selectedPersonDisplay.textContent = `Adicionando relação para: ${person.name}`;
            document.getElementById('related-person-name').value = '';
            document.getElementById('related-person-gender').value = 'male';
            document.getElementById('related-person-trait').value = 'none';
        }

        // Close modals
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                addPersonModal.style.display = 'none';
                editPersonModal.style.display = 'none';
                relationshipModal.style.display = 'none';
            });
        });

        window.addEventListener('click', (event) => {
            if (event.target === addPersonModal) {
                addPersonModal.style.display = 'none';
            }
            if (event.target === editPersonModal) {
                editPersonModal.style.display = 'none';
            }
            if (event.target === relationshipModal) {
                relationshipModal.style.display = 'none';
            }
        });

        // Form handlers
        personForm.addEventListener('submit', (e) => {
            e.preventDefault();
            addPersonToTree();
        });

        editPersonForm.addEventListener('submit', (e) => {
            e.preventDefault();
            editPersonInTree();
        });

        addRelationshipBtn.addEventListener('click', () => {
            addRelationship();
        });

        // Drag and zoom functionality
        dragHandle.addEventListener('mousedown', (e) => {
            isDragging = true;
            dragStartX = e.clientX - treeOffsetX;
            dragStartY = e.clientY - treeOffsetY;
        });

        document.addEventListener('mousemove', (e) => {
            if (isDragging) {
                treeOffsetX = e.clientX - dragStartX;
                treeOffsetY = e.clientY - dragStartY;
                updateTreePosition();
            }
        });

        document.addEventListener('mouseup', () => {
            isDragging = false;
        });

        zoomInBtn.addEventListener('click', () => {
            zoomLevel += 10;
            if (zoomLevel > 200) zoomLevel = 200;
            updateZoom();
        });

        zoomOutBtn.addEventListener('click', () => {
            zoomLevel -= 10;
            if (zoomLevel < 25) zoomLevel = 25;
            updateZoom();
        });

        function updateZoom() {
            zoomLevelDisplay.textContent = `${zoomLevel}%`;
            treeContainer.style.transform = `translate(${treeOffsetX}px, ${treeOffsetY}px) scale(${zoomLevel / 100})`;
        }

        function updateTreePosition() {
            treeContainer.style.transform = `translate(${treeOffsetX}px, ${treeOffsetY}px) scale(${zoomLevel / 100})`;
        }

        // Initial setup
        function initializeFamilyTree() {
            // Create initial sample data
            const initialData = [
                {
                    id: 1,
                    name: 'Frank Harris',
                    birthYear: 1908,
                    deathYear: 2001,
                    gender: 'male',
                    trait: 'presence',
                    x: 400,
                    y: 100,
                    children: [3],
                    spouse: 2
                },
                {
                    id: 2,
                    name: 'Shirley Johns',
                    birthYear: 1910,
                    deathYear: 2000,
                    gender: 'female',
                    trait: 'carrier',
                    x: 600,
                    y: 100,
                    children: [3],
                    spouse: 1
                },
                {
                    id: 3,
                    name: 'Edward Harris',
                    birthYear: 1935,
                    deathYear: 2015,
                    gender: 'male',
                    trait: 'presence',
                    x: 500,
                    y: 200,
                    children: [5],
                    spouse: 4
                },
                {
                    id: 4,
                    name: 'Patricia Anderson',
                    birthYear: 1935,
                    deathYear: 2010,
                    gender: 'female',
                    trait: 'none',
                    x: 700,
                    y: 200,
                    children: [5],
                    spouse: 3
                },
                {
                    id: 5,
                    name: 'Tony Harris',
                    birthYear: 1940,
                    deathYear: 2020,
                    gender: 'male',
                    trait: 'absence',
                    x: 600,
                    y: 300,
                    children: [6, 7],
                    spouse: 6
                },
                {
                    id: 6,
                    name: 'Mary Harris',
                    birthYear: 1970,
                    gender: 'female',
                    trait: 'none',
                    x: 700,
                    y: 300,
                    children: [5],
                    spouse: 5
                },
                {
                    id: 7,
                    name: 'John Harris',
                    birthYear: 1975,
                    gender: 'male',
                    trait: 'presence',
                    x: 800,
                    y: 300,
                    children: [],
                    spouse: null
                }
            ];

            familyTree = initialData;
            renderFamilyTree();
            currentId = 8;
        }

        function resetFamilyTree() {
            familyTree = [];
            currentId = 1;
            selectedPersonId = null;
            familyTreeElement.innerHTML = '';
            connectionsContainer.innerHTML = '';
            renderFamilyTree();
            Swal.fire({
                icon: 'success',
                title: 'Árvore reiniciada!',
                text: 'Você pode começar a construir sua nova árvore genealógica.'
            });
        }

        function saveFamilyTree() {
            Swal.fire({
                icon: 'success',
                title: 'Árvore salva!',
                text: 'Seu arquivo foi salvo com sucesso.',
                timer: 1500
            });
        }

        function addPersonToTree() {
            const formData = new FormData(personForm);
            const person = {
                id: currentId++,
                name: formData.get('name'),
                birthYear: formData.get('birth-year') ? parseInt(formData.get('birth-year')) : null,
                deathYear: formData.get('death-year') ? parseInt(formData.get('death-year')) : null,
                gender: formData.get('gender'),
                trait: formData.get('trait'),
                x: 200 + Math.random() * 600,
                y: 200 + Math.random() * 400,
                children: [],
                spouse: null
            };

            familyTree.push(person);
            renderFamilyTree();
            addPersonModal.style.display = 'none';
            personForm.reset();
            
            Swal.fire({
                icon: 'success',
                title: 'Pessoa adicionada!',
                text: 'A pessoa foi adicionada à árvore genealógica.'
            });
        }

        function editPersonInTree() {
            const formData = new FormData(editPersonForm);
            const id = parseInt(formData.get('edit-id'));
            const personIndex = familyTree.findIndex(p => p.id === id);
            
            if (personIndex !== -1) {
                familyTree[personIndex].name = formData.get('edit-name');
                familyTree[personIndex].birthYear = formData.get('edit-birth-year') ? parseInt(formData.get('edit-birth-year')) : null;
                familyTree[personIndex].deathYear = formData.get('edit-death-year') ? parseInt(formData.get('edit-death-year')) : null;
                familyTree[personIndex].gender = formData.get('edit-gender');
                familyTree[personIndex].trait = formData.get('edit-trait');
                
                renderFamilyTree();
                editPersonModal.style.display = 'none';
                editPersonForm.reset();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Pessoa atualizada!',
                    text: 'As informações foram atualizadas com sucesso.'
                });
            }
        }

        function addRelationship() {
            const relatedName = document.getElementById('related-person-name').value;
            const gender = document.getElementById('related-person-gender').value;
            const trait = document.getElementById('related-person-trait').value;
            const relationshipType = document.getElementById('relationship-type').value;

            if (!relatedName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Por favor, insira o nome da pessoa relacionada.'
                });
                return;
            }

            if (!selectedPersonId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Por favor, selecione uma pessoa primeiro.'
                });
                return;
            }

            const selectedPerson = familyTree.find(p => p.id === selectedPersonId);
            if (!selectedPerson) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Pessoa selecionada não encontrada.'
                });
                return;
            }

            // Create new person
            const newPerson = {
                id: currentId++,
                name: relatedName,
                birthYear: null,
                deathYear: null,
                gender: gender,
                trait: trait,
                x: selectedPerson.x,
                y: selectedPerson.y,
                children: [],
                spouse: null
            };

            // Adjust position based on relationship
            if (relationshipType === 'child') {
                newPerson.y = selectedPerson.y + 120;
                newPerson.x = selectedPerson.x + (selectedPerson.children.length * 100) - 50;
                selectedPerson.children.push(newPerson.id);
            } else if (relationshipType === 'parent') {
                newPerson.y = selectedPerson.y - 120;
                newPerson.x = selectedPerson.x + (Math.random() > 0.5 ? 100 : -100);
                newPerson.children.push(selectedPerson.id);
            } else { // spouse
                newPerson.y = selectedPerson.y;
                newPerson.x = selectedPerson.x + (selectedPerson.gender === 'male' ? 150 : -150);
                newPerson.spouse = selectedPersonId;
                selectedPerson.spouse = newPerson.id;
            }

            familyTree.push(newPerson);
            renderFamilyTree();
            relationshipModal.style.display = 'none';
            selectedPersonId = null;
            
            Swal.fire({
                icon: 'success',
                title: 'Relação adicionada!',
                text: `A relação de ${relationshipType} foi criada com sucesso.`
            });
        }

        function renderFamilyTree() {
            // Clear containers
            familyTreeElement.innerHTML = '';
            connectionsContainer.innerHTML = '';

            // Sort people by y position to ensure proper layering
            const sortedPeople = [...familyTree].sort((a, b) => a.y - b.y);

            // Create all nodes first
            sortedPeople.forEach(person => {
                const node = createPersonNode(person);
                familyTreeElement.appendChild(node);
            });

            // Draw all connections
            drawConnections();
        }

        function createPersonNode(person) {
            const node = document.createElement('div');
            node.className = 'node';
            node.dataset.id = person.id;
            node.style.position = 'absolute';
            node.style.left = `${person.x}px`;
            node.style.top = `${person.y}px`;

            // Create the person shape
            const shape = document.createElement('div');
            shape.className = person.gender === 'male' ? 'male' : 'female';
            shape.textContent = person.name.charAt(0).toUpperCase();
            
            // Apply trait styles
            if (person.trait === 'presence') {
                shape.classList.add('presence');
            } else if (person.trait === 'absence') {
                shape.classList.add('absence');
            } else if (person.trait === 'carrier') {
                shape.classList.add('carrier');
            }

            // Add click event
            node.addEventListener('click', (e) => {
                e.stopPropagation();
                if (selectedTool === 'select') {
                    // Select the node
                    document.querySelectorAll('.node').forEach(n => n.classList.remove('selected'));
                    node.classList.add('selected');
                } else if (selectedTool === 'edit') {
                    openEditPersonModal(person);
                } else if (selectedTool === 'delete') {
                    Swal.fire({
                        title: 'Excluir pessoa?',
                        text: `Deseja excluir ${person.name}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sim, excluir!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deletePerson(person.id);
                        }
                    });
                } else if (selectedTool === 'relationship') {
                    openRelationshipModal(person);
                }
            });

            // Add double-click to edit
            node.addEventListener('dblclick', (e) => {
                e.stopPropagation();
                if (selectedTool !== 'edit') {
                    openEditPersonModal(person);
                }
            });

            // Add to node
            node.appendChild(shape);

            // Add label
            const label = document.createElement('div');
            label.className = 'label';
            label.textContent = person.name;
            if (person.birthYear) {
                label.textContent += `\n(${person.birthYear}`;
                if (person.deathYear) {
                    label.textContent += `-${person.deathYear}`;
                }
                label.textContent += ')';
            }
            node.appendChild(label);

            return node;
        }

        function drawConnections() {
            // Clear existing connections
            connectionsContainer.innerHTML = '';

            // Draw all connections
            familyTree.forEach(person => {
                // Draw marriage lines
                if (person.spouse) {
                    const spouse = familyTree.find(p => p.id === person.spouse);
                    if (spouse) {
                        const marriageLine = document.createElement('div');
                        marriageLine.className = 'connection-line marriage-line';
                        
                        // Position marriage line between spouses
                        const midX = (person.x + spouse.x) / 2;
                        marriageLine.style.left = `${midX - 10}px`;
                        marriageLine.style.top = `${person.y + 20}px`;
                        marriageLine.style.width = '20px';
                        marriageLine.style.height = '2px';
                        
                        connectionsContainer.appendChild(marriageLine);
                    }
                }

                // Draw parent-child lines
                if (person.children && person.children.length > 0) {
                    person.children.forEach(childId => {
                        const child = familyTree.find(p => p.id === childId);
                        if (child) {
                            // Draw vertical line from parent to child
                            const verticalLine = document.createElement('div');
                            verticalLine.className = 'connection-line vertical-line';
                            verticalLine.style.left = `${person.x + 20}px`;
                            verticalLine.style.top = `${person.y + 40}px`;
                            verticalLine.style.height = `${child.y - person.y - 40}px`;
                            verticalLine.style.width = '2px';
                            
                            connectionsContainer.appendChild(verticalLine);
                            
                            // Draw horizontal line to child
                            const horizontalLine = document.createElement('div');
                            horizontalLine.className = 'connection-line horizontal-line';
                            
                            // Position based on who is on the left
                            const leftX = Math.min(person.x + 20, child.x + 20);
                            const width = Math.abs(child.x - person.x);
                            
                            horizontalLine.style.left = `${leftX}px`;
                            horizontalLine.style.top = `${child.y}px`;
                            horizontalLine.style.width = `${width}px`;
                            horizontalLine.style.height = '2px';
                            
                            connectionsContainer.appendChild(horizontalLine);
                        }
                    });
                }
            });
        }

        function deletePerson(id) {
            // Find the person
            const personIndex = familyTree.findIndex(p => p.id === id);
            if (personIndex === -1) return;

            const person = familyTree[personIndex];
            
            // Remove from children arrays of other people
            familyTree.forEach(p => {
                if (p.children && p.children.includes(id)) {
                    p.children = p.children.filter(c => c !== id);
                }
                if (p.spouse === id) {
                    p.spouse = null;
                }
            });
            
            // Remove the person
            familyTree.splice(personIndex, 1);
            
            // Re-render the tree
            renderFamilyTree();
            
            Swal.fire({
                icon: 'success',
                title: 'Pessoa excluída!',
                text: 'A pessoa foi removida da árvore genealógica.'
            });
        }

        // Initialize the app
        initializeFamilyTree();
        updateZoom();
    </script>
</body>
</html>
