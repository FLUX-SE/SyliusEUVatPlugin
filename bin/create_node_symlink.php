<?php

declare(strict_types=1);

const NODE_MODULES_FOLDER_NAME = 'node_modules';
const OLD_PATH_TO_NODE_MODULES = 'tests' . \DIRECTORY_SEPARATOR . 'Application' . \DIRECTORY_SEPARATOR . 'node_modules';
const NEW_PATH_TO_NODE_MODULES = 'vendor' . \DIRECTORY_SEPARATOR . 'sylius' . \DIRECTORY_SEPARATOR . 'test-application' . \DIRECTORY_SEPARATOR . 'node_modules';

if (@lstat(NODE_MODULES_FOLDER_NAME)) {
    if (is_link(NODE_MODULES_FOLDER_NAME)) {
        $currentTarget = @readlink(NODE_MODULES_FOLDER_NAME);
        $currentRealPath = $currentTarget ? realpath($currentTarget) : false;
        $newRealPath = realpath(NEW_PATH_TO_NODE_MODULES);

        /* Already points to the correct target */
        if ($currentRealPath && $newRealPath && $currentRealPath === $newRealPath) {
            echo '> `' . NODE_MODULES_FOLDER_NAME . '` already points to the correct target.' . \PHP_EOL;
            exit(0);
        }

        /* Points to the old known symlink target — safe to replace */
        $oldRealPath = realpath(OLD_PATH_TO_NODE_MODULES);
        $isOldTarget = ($currentTarget === OLD_PATH_TO_NODE_MODULES)
            || ($currentRealPath && $oldRealPath && $currentRealPath === $oldRealPath);

        if (!$isOldTarget) {
            echo '> `' . NODE_MODULES_FOLDER_NAME . '` exists as a link pointing elsewhere, keeping existing as may be intentional.' . \PHP_EOL;
            exit(0);
        }

        echo '> Replacing old symlink pointing to `' . OLD_PATH_TO_NODE_MODULES . '`...' . \PHP_EOL;
        if (!@unlink(NODE_MODULES_FOLDER_NAME)) {
            echo '> Could not delete old symlink `' . NODE_MODULES_FOLDER_NAME . '`.' . \PHP_EOL;
            exit(1);
        }
    } elseif (is_dir(NODE_MODULES_FOLDER_NAME)) {
        echo '> `' . NODE_MODULES_FOLDER_NAME . '` exists as a real directory, keeping existing as may be intentional.' . \PHP_EOL;
        exit(0);
    } else {
        echo '> Invalid `' . NODE_MODULES_FOLDER_NAME . '` detected, recreating...' . \PHP_EOL;
        if (!@unlink(NODE_MODULES_FOLDER_NAME)) {
            echo '> Could not delete file `' . NODE_MODULES_FOLDER_NAME . '`.' . \PHP_EOL;
            exit(1);
        }
    }
}

/* try to create the symlink using PHP internals... */
$success = @symlink(NEW_PATH_TO_NODE_MODULES, NODE_MODULES_FOLDER_NAME);

/* if case it has failed, but OS is Windows... */
if (!$success && strtoupper(substr(\PHP_OS, 0, 3)) === 'WIN') {
    /* ...then try a different approach which does not require elevated permissions and folder to exist */
    echo '> This system is running Windows, creation of links requires elevated privileges,' . \PHP_EOL;
    echo '> and target path to exist. Fallback to NTFS Junction:' . \PHP_EOL;
    exec(sprintf('mklink /J %s %s 2> NUL', NODE_MODULES_FOLDER_NAME, NEW_PATH_TO_NODE_MODULES), $output, $returnCode);
    $success = $returnCode === 0;
    if (!$success) {
        echo '> Failed to create the required symlink' . \PHP_EOL;
        exit(2);
    }
}

$path = @readlink(NODE_MODULES_FOLDER_NAME);
/* check if link points to the intended directory */
if ($path && realpath($path) === realpath(NEW_PATH_TO_NODE_MODULES)) {
    echo '> Successfully created the symlink.' . \PHP_EOL;
    exit(0);
}

echo '> Failed to create the symlink to `' . NODE_MODULES_FOLDER_NAME . '`.' . \PHP_EOL;
exit(3);
