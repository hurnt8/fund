#!/usr/bin/env node
/**
 * docx2html.js — Open-source DOCX→HTML converter using mammoth.js
 * Usage: node tools/docx2html.js <path/to/file.docx>
 * Output: full HTML document on stdout
 */
'use strict';

const mammoth = require('mammoth');
const path    = require('path');
const fs      = require('fs');

const docxPath = process.argv[2];
if (!docxPath) {
  process.stderr.write('Usage: node docx2html.js <docx-path>\n');
  process.exit(1);
}
if (!fs.existsSync(docxPath)) {
  process.stderr.write('File not found: ' + docxPath + '\n');
  process.exit(1);
}

// ── Style map: Word styles → HTML elements ──────────────────────────────────
const styleMap = [
  // Headings (FR + EN)
  "p[style-name='Heading 1']   => h1:fresh",
  "p[style-name='Heading 2']   => h2:fresh",
  "p[style-name='Heading 3']   => h3:fresh",
  "p[style-name='Heading 4']   => h4:fresh",
  "p[style-name='Titre 1']     => h1:fresh",
  "p[style-name='Titre 2']     => h2:fresh",
  "p[style-name='Titre 3']     => h3:fresh",
  "p[style-name='Titre']       => h1:fresh",
  "p[style-name='Title']       => h1:fresh",
  "p[style-name='Subtitle']    => h2:fresh",
  "p[style-name='Sous-titre']  => h2:fresh",

  // Character styles
  "b       => strong",
  "i       => em",
  "u       => u",
  "strike  => s",

  // Page breaks → marker detected by PHP splitIntoPages()
  "br[type='page'] => hr.page-break-before",
].join('\n');

// ── Image conversion: embed as base64 ───────────────────────────────────────
const convertImage = mammoth.images.imgElement(function (image) {
  return image.read('base64').then(function (b64) {
    return {
      src:   'data:' + image.contentType + ';base64,' + b64,
      style: 'max-width:100%;height:auto',
    };
  });
});

// ── Run conversion ───────────────────────────────────────────────────────────
mammoth.convertToHtml({ path: docxPath }, {
  styleMap,
  convertImage,
  includeDefaultStyleMap: true,
  ignoreEmptyParagraphs:  false,
})
.then(function (result) {
  if (result.messages && result.messages.length) {
    result.messages.forEach(function (m) {
      if (m.type === 'error') process.stderr.write('[mammoth] ' + m.message + '\n');
    });
  }

  // Wrap in a full HTML document with baseline DOCX styling
  const html = [
    '<!DOCTYPE html>',
    '<html><head><meta charset="UTF-8">',
    '<style>',
    'body{font-size:11pt;line-height:1.5;color:#000;margin:0;padding:0}',
    'h1{font-size:16pt;font-weight:700;margin:.6em 0 .3em}',
    'h2{font-size:14pt;font-weight:700;margin:.5em 0 .25em}',
    'h3{font-size:12pt;font-weight:700;margin:.4em 0 .2em}',
    'h4{font-size:11pt;font-weight:700;margin:.3em 0 .15em}',
    'p{margin:.2em 0;padding:0}',
    'table{border-collapse:collapse;width:100%;margin:.5em 0}',
    'td,th{border:1px solid #ccc;padding:4px 8px;vertical-align:top}',
    'th{background:#f0f0f0;font-weight:700}',
    'ul{list-style-type:disc;padding-left:2em;margin:.3em 0}',
    'ol{list-style-type:decimal;padding-left:2em;margin:.3em 0}',
    'li{margin:.1em 0}',
    'strong{font-weight:700}',
    'em{font-style:italic}',
    'u{text-decoration:underline}',
    's{text-decoration:line-through}',
    'img{display:block;margin:6px auto;max-width:100%}',
    'hr.page-break-before{visibility:hidden;height:0;margin:0;padding:0;border:none;page-break-before:always}',
    '</style>',
    '</head>',
    '<body>',
    result.value,
    '</body></html>',
  ].join('\n');

  process.stdout.write(html);
})
.catch(function (err) {
  process.stderr.write('mammoth fatal: ' + err.message + '\n');
  process.exit(1);
});
