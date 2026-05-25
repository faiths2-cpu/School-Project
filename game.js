const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");

// Game variables
let player = { 
    x: 50, 
    y: 300, 
    width: 40, 
    height: 40, 
    dy: 0, 
    jumpPower: -12, 
    gravity: 0.8,
    isJumping: false
};
let obstacles = [];
let score = 0;
let gameOver = false;
let gameRunning = false;
let speedster = "default";
let gameLoop;
let obstacleInterval;

// Character colors with better visual design
const characterColors = {
    default: "#00ff9d", // Neon green
    red: "#ff4757",     // Bright red
    blue: "#2ed8ff",    // Sky blue
    green: "#00ff9d"    // Same as default for now
};

// Game initialization
document.getElementById("loginForm")?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const name = document.getElementById("nameInput").value.trim();
    speedster = document.getElementById("characterSelect").value;
    
    if (!name) {
        alert("Please enter your name!");
        return;
    }

    // Hide login screen and show game
    document.getElementById("loginScreen").style.display = "none";
    canvas.style.display = "block";
    document.getElementById("gameStats").style.display = "block";

    // Reset game state
    resetGame();
    
    // Start the game
    runGame(name, speedster);
});

function resetGame() {
    player = { 
        x: 50, 
        y: 300, 
        width: 40, 
        height: 40, 
        dy: 0, 
        jumpPower: -12, 
        gravity: 0.8,
        isJumping: false
    };
    obstacles = [];
    score = 0;
    gameOver = false;
    gameRunning = true;
    
    // Clear any existing intervals
    if (obstacleInterval) {
        clearInterval(obstacleInterval);
    }
    if (gameLoop) {
        cancelAnimationFrame(gameLoop);
    }
}

function runGame(playerName, chosenSpeedster) {
    // Event listeners
    document.addEventListener("keydown", handleKeyDown);
    
    // Start obstacle spawning
    obstacleInterval = setInterval(spawnObstacle, 1500);
    
    // Start game loop
    gameLoop = requestAnimationFrame(update);
    
    function handleKeyDown(e) {
        if (e.code === "Space" && !player.isJumping && gameRunning) {
            player.dy = player.jumpPower;
            player.isJumping = true;
        }
    }
    
    function spawnObstacle() {
        if (!gameRunning) return;
        
        const obstacleTypes = [
            { width: 30, height: 30, y: 320 }, // Small cube
            { width: 40, height: 20, y: 330 }, // Low rectangle
            { width: 25, height: 40, y: 310 }  // Tall thin
        ];
        
        const type = obstacleTypes[Math.floor(Math.random() * obstacleTypes.length)];
        obstacles.push({ 
            x: canvas.width, 
            y: type.y, 
            width: type.width, 
            height: type.height,
            color: "#ff6b6b"
        });
    }
    
    function update() {
        if (!gameRunning) return;
        
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Draw background
        drawBackground();
        
        // Player physics
        updatePlayer();
        
        // Update and draw obstacles
        updateObstacles();
        
        // Update score
        updateScore();
        
        // Draw ground
        drawGround();
        
        // Continue game loop
        gameLoop = requestAnimationFrame(update);
    }
    
    function drawBackground() {
        // Gradient background
        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, "#6a11cb");
        gradient.addColorStop(1, "#2575fc");
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, canvas.height);
    }
    
    function updatePlayer() {
        // Apply gravity
        player.dy += player.gravity;
        player.y += player.dy;
        
        // Ground collision
        if (player.y > 300) {
            player.y = 300;
            player.dy = 0;
            player.isJumping = false;
        }
        
        // Ceiling collision
        if (player.y < 0) {
            player.y = 0;
            player.dy = 0;
        }
        
        // Draw player with better graphics
        drawPlayer();
    }
    
    function drawPlayer() {
        ctx.fillStyle = characterColors[chosenSpeedster];
        
        // Player shadow
        ctx.fillStyle = "rgba(0, 0, 0, 0.3)";
        ctx.fillRect(player.x + 5, player.y + 5, player.width, player.height);
        
        // Main player body
        ctx.fillStyle = characterColors[chosenSpeedster];
        ctx.fillRect(player.x, player.y, player.width, player.height);
        
        // Player details (eyes)
        ctx.fillStyle = "#1a1a2e";
        ctx.fillRect(player.x + 25, player.y + 10, 8, 8);
    }
    
    function updateObstacles() {
        for (let i = obstacles.length - 1; i >= 0; i--) {
            const obs = obstacles[i];
            
            // Move obstacle
            obs.x -= 5 + Math.floor(score / 500); // Speed increases with score
            
            // Remove off-screen obstacles
            if (obs.x + obs.width < 0) {
                obstacles.splice(i, 1);
                continue;
            }
            
            // Draw obstacle with shadow
            ctx.fillStyle = "rgba(0, 0, 0, 0.3)";
            ctx.fillRect(obs.x + 3, obs.y + 3, obs.width, obs.height);
            
            ctx.fillStyle = obs.color;
            ctx.fillRect(obs.x, obs.y, obs.width, obs.height);
            
            // Collision detection
            if (checkCollision(player, obs)) {
                endGame(playerName, chosenSpeedster);
                return;
            }
        }
    }
    
    function checkCollision(player, obstacle) {
        return (
            player.x < obstacle.x + obstacle.width &&
            player.x + player.width > obstacle.x &&
            player.y < obstacle.y + obstacle.height &&
            player.y + player.height > obstacle.y
        );
    }
    
    function updateScore() {
        score++;
        
        // Update UI
        document.getElementById("scoreValue").textContent = score;
        document.getElementById("distanceValue").textContent = Math.floor(score / 10) + "m";
        document.getElementById("speedValue").textContent = (1 + Math.floor(score / 500) * 0.1).toFixed(1) + "x";
    }
    
    function drawGround() {
        ctx.fillStyle = "#1a1a2e";
        ctx.fillRect(0, 340, canvas.width, 60);
        
        // Ground pattern
        ctx.fillStyle = "#00ff9d";
        for (let i = 0; i < canvas.width; i += 40) {
            ctx.fillRect(i, 340, 20, 5);
        }
    }
}

async function endGame(playerName, chosenSpeedster) {
    gameRunning = false;
    gameOver = true;
    
    // Clear intervals
    clearInterval(obstacleInterval);
    cancelAnimationFrame(gameLoop);
    
    // Calculate final stats
    const finalScore = score;
    const distance = Math.floor(score / 10);
    
    // Hide game elements
    canvas.style.display = "none";
    document.getElementById("gameStats").style.display = "none";
    
    // Show leaderboard
    document.getElementById("leaderboard").style.display = "block";
    
    try {
        // Save score to DB
        await fetch("../api/submit_score.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ 
                name: playerName, 
                speedster: chosenSpeedster, 
                score: finalScore, 
                distance: distance 
            })
        });
        
        // Load and display leaderboard
        await loadLeaderboard();
    } catch (error) {
        console.error("Error saving score:", error);
        // Fallback: Show local leaderboard
        showLocalLeaderboard(playerName, finalScore, distance);
    }
}

async function loadLeaderboard() {
    try {
        const res = await fetch("../api/leaderboard.php");
        const data = await res.json();
        displayLeaderboard(data);
    } catch (error) {
        console.error("Error loading leaderboard:", error);
        // Fallback to local storage
        const localData = getLocalLeaderboard();
        displayLeaderboard(localData);
    }
}

function getLocalLeaderboard() {
    // Get from localStorage or return default data
    const stored = localStorage.getItem('pixelDashLeaderboard');
    if (stored) {
        return JSON.parse(stored);
    }
    
    // Default leaderboard data
    return [
        { name: "PixelPro", speedster: "default", score: 1500, distance: 150 },
        { name: "DashMaster", speedster: "red", score: 1200, distance: 120 },
        { name: "GameChamp", speedster: "blue", score: 900, distance: 90 }
    ];
}

function displayLeaderboard(data) {
    const tbody = document.getElementById("leaderboardBody");
    tbody.innerHTML = "";
    
    // Sort by score (descending)
    data.sort((a, b) => b.score - a.score);
    
    // Display top 10
    data.slice(0, 10).forEach((row, index) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${row.name}</td>
            <td>${row.speedster}</td>
            <td>${row.score}</td>
            <td>${row.distance}m</td>
        `;
        
        // Highlight current player (you'd need to track this)
        if (index === 2) { // Example highlight
            tr.style.backgroundColor = "rgba(0, 255, 157, 0.1)";
        }
        
        tbody.appendChild(tr);
    });
}

function showLocalLeaderboard(playerName, score, distance) {
    const tbody = document.getElementById("leaderboardBody");
    tbody.innerHTML = "";
    
    const leaderboardData = [
        { name: "PixelPro", speedster: "default", score: 1500, distance: 150 },
        { name: "DashMaster", speedster: "red", score: 1200, distance: 120 },
        { name: playerName, speedster: "default", score: score, distance: distance },
        { name: "GameChamp", speedster: "blue", score: 900, distance: 90 },
        { name: "RetroRunner", speedster: "green", score: 750, distance: 75 }
    ];
    
    displayLeaderboard(leaderboardData);
}

// Restart game function (called from HTML)
function restartGame() {
    document.getElementById("leaderboard").style.display = "none";
    document.getElementById("loginScreen").style.display = "block";
    
    // Reset form
    document.getElementById("loginForm").reset();
}

// Prevent space bar from scrolling page
window.addEventListener("keydown", function(e) {
    if(e.code === "Space" && e.target === document.body) {
        e.preventDefault();
    }
});