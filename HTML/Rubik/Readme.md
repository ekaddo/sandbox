
Build a single-file, photorealistic interactive 3D Rubik's Cube web application (`index.html`) using Three.js (loaded via ES module CDN) with real-state solving and a modern glassmorphic studio UI.

### 1. Visuals & Photorealistic Rendering
- **Cube Geometry:** Build a 3×3×3 cube composed of 27 beveled matte-charcoal cubies (`RoundedBoxGeometry`) with extruded, rounded-corner colored stickers (standard WCA color scheme: white, yellow, red, orange, blue, green).
- **Materials & Lighting:** Use `MeshPhysicalMaterial` with glossy clearcoat on the stickers and subtle roughness on the plastic body. Configure `ACESFilmicToneMapping`, a procedural `RoomEnvironment` PMREM studio reflection map, and three-point directional lighting over a dark radial studio backdrop.
- **Zero Drift:** After every layer rotation completes, snap each cubie's position and quaternion to an exact integer grid and orthogonal 90° orientation so floating-point drift never accumulates.

### 2. Direct 3D Interaction & Controls
- **Face Dragging:** Implement raycasting on cubie faces. Project the user's screen-space pointer drag onto the clicked face's local 3D tangent axes to determine the intended slice (outer or middle layer) and rotation direction.
- **Orbit Locking:** Use `OrbitControls` for smooth camera orbiting when dragging the background, and automatically lock orbiting while a face twist is in progress.
- **Animations:** Support smooth eased 90° and 180° turns with adjustable playback speed.

### 3. Real-State Tracking & Dual Solvers
- **Live 54-Sticker State:** Maintain an accurate 54-sticker state model synchronized with every manual drag, scramble, and solver move. Never "cheat" by reversing move history.
- **WCA Scramble:** Generate valid 20-move random-turn scrambles (avoiding redundant consecutive or opposite-face moves).
- **Dual Solvers:**
  1. **Kociemba Two-Phase Solver** (e.g., via `cubejs` CDN) for near-optimal ~20-move solutions, displaying Phase 1 (`G0 → G1`) and Phase 2 (`G1 → Solved`) badges.
  2. **Built-in Pure-JS Layer-by-Layer Solver** as a self-contained secondary option.
- **Verification:** Validate every generated solution against a cloned state before animating it on the 3D cube.

### 4. Glassmorphic Studio UI
- **Top-Left HUD:** Minimal 3×3 grid logo, title ("RUBIK'S 3D"), live status pill (Solved / Scrambled / Solving / Dragging), plus counters for `MOVES`, `TIME`, and active `SOLVER`.
- **Bottom-Right Panel:** Tabbed glassmorphic panel with:
  - `Moves`: Scrollable move notation history.
  - `Net`: Live-synced 2D unfolded cross net of all 54 stickers.
  - `i` (Info): Quick guide to mouse/touch controls and solver details.
- **Move Sequence Bar:** Floating bar showing the active scramble or solution queue, highlighting the current move in real time with phase badges.
- **Bottom Control Dock:** Buttons for `Scramble`, `Real Solve`, `Pause / Resume`, and `Reset`, along with `SOLVER` (Kociemba / Layer-by-Layer) and `SPEED` selectors.

Deliver the complete, self-contained HTML file ready to open directly in a browser with no build step required.
