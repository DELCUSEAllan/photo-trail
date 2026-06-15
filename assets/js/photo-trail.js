document.addEventListener("DOMContentLoaded", () => {
	const config = window.photoTrailConfig;

	if (
		!config ||
		!Array.isArray(config.images) ||
		config.images.length === 0
	) {
		return;
	}

	const container = document.createElement("div");
	container.id = "photo-trail-container";
	document.body.append(container);

	const spawnDelay = Number(config.spawnDelay) || 180;
	const animationTime = Number(config.animationTime) || 3500;
	const imageSize = Number(config.imageSize) || 240;

	document.documentElement.style.setProperty(
		"--photo-trail-image-size",
		`${imageSize}px`
	);

	let lastSpawn = 0;

	const createTrailImage = (event) => {
		const now = performance.now();

		if (now - lastSpawn < spawnDelay) {
			return;
		}

		lastSpawn = now;

		const imageUrl =
			config.images[Math.floor(Math.random() * config.images.length)];

		const image = document.createElement("img");

		image.src = imageUrl;
		image.alt = "";
		image.loading = "lazy";
		image.decoding = "async";
		image.className = "photo-trail-image";

		image.style.left = `${event.clientX}px`;
		image.style.top = `${event.clientY}px`;

		container.append(image);

		const startRotation = Math.random() * 12 - 6;
		const endRotation = Math.random() * 16 - 8;

		const animation = image.animate(
			[
				{
					opacity: 0,
					transform: `translate(-50%, -50%) scale(0.85) rotate(${startRotation}deg)`
				},
				{
					opacity: 1,
					transform: "translate(-50%, -50%) scale(1) rotate(0deg)",
					offset: 0.15
				},
				{
					opacity: 1,
					transform: "translate(-50%, -50%) scale(1) rotate(0deg)",
					offset: 0.8
				},
				{
					opacity: 0,
					transform: `translate(-50%, -50%) scale(1.05) rotate(${endRotation}deg)`
				}
			],
			{
				duration: animationTime,
				easing: "ease",
				fill: "forwards"
			}
		);

		animation.finished
			.then(() => image.remove())
			.catch(() => image.remove());
	};

	document.addEventListener("mousemove", createTrailImage);
});
