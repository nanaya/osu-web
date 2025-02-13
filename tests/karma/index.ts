// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

// This builds the tests into a single bundle.

import '../../resources/builds/locales/en';

// Doesn't work when specified in karma config for some reason.
import '../../resources/js/entrypoints/app';

const testsContext = require.context('.', true, /\.spec$/);
testsContext.keys().forEach(testsContext);
