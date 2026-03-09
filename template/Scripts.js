document.addEventListener('DOMContentLoaded', function() {
    const startGameButton = document.getElementById('start-game');
    const endTurnButton = document.getElementById('end-turn');
    const gameStatusSection = document.getElementById('game-status');

    startGameButton.addEventListener('click', function() {
        gameStatusSection.innerText = 'Game Started!';
    });

    endTurnButton.addEventListener('click', function() {
        gameStatusSection.innerText = 'Turn Ended!';
    });
});
