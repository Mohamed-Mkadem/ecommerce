import assert from "node:assert/strict";
import { spawnSync } from "node:child_process";
import { existsSync, readFileSync } from "node:fs";

export function resolvePhpBinary() {
    if (process.env.PHP_BINARY) return process.env.PHP_BINARY;
    if (process.platform !== "win32") return "php";

    // Herd exposes PHP through a .bat shim, which Node cannot spawn directly.
    const command = spawnSync("where.exe", ["php"], { encoding: "utf8" });
    const shimPath = command.stdout?.split(/\r?\n/).find((path) => path.trim())?.trim();
    if (command.status === 0 && shimPath?.toLowerCase().endsWith(".bat")) {
        const shim = readFileSync(shimPath, "utf8");
        const executablePath = shim.match(/"([^\"]+\.exe)"/i)?.[1];
        if (executablePath && existsSync(executablePath)) return executablePath;
    }

    return "php";
}

export function assertLaravelPhpRuntime(phpBinary) {
    const result = spawnSync(phpBinary, ["-r", "echo PHP_VERSION;"], { encoding: "utf8" });
    assert.equal(
        result.status,
        0,
        `Could not run PHP executable "${phpBinary}": ${result.error?.message || result.stderr}`,
    );

    const [major, minor, patch] = result.stdout.trim().split(".").map(Number);
    assert.ok(
        major > 8 || (major === 8 && (minor > 4 || (minor === 4 && patch >= 1))),
        `The Laravel 12 E2E support scripts need PHP >= 8.4.1. Found ${result.stdout.trim()} from "${phpBinary}". Set PHP_BINARY to your PHP 8.4 executable or put it first on PATH.`,
    );
}
