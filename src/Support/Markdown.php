<?php
declare(strict_types=1);

namespace Tefeyyuz\Support;

use Parsedown;

final class Markdown
{
    private Parsedown $parser;

    public function __construct()
    {
        $this->parser = new Parsedown();
        $this->parser->setSafeMode(false);
        $this->parser->setBreaksEnabled(false);
    }

    public function toHtml(string $markdown): string
    {
        $markdown = $this->shortcodes($markdown);
        $markdown = $this->tables($markdown);
        $html = $this->parser->text($markdown);
        return $this->headingIds($html);
    }

    public function toc(string $html): string
    {
        if (!preg_match_all('/<h([23]) id="([^"]+)"[^>]*>(.*?)<\/h\1>/u', $html, $m, PREG_SET_ORDER)) {
            return '';
        }
        $out = '<nav><ul>';
        $openH3 = false;
        foreach ($m as $row) {
            $text = trim(strip_tags($row[3]));
            $text = preg_replace('/\s*#$/', '', $text) ?? $text;
            if ($row[1] === '2') {
                if ($openH3) {
                    $out .= '</ul></li>';
                    $openH3 = false;
                }
                $out .= '<li><a href="#' . htmlspecialchars($row[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">' . htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
            } else {
                if (!$openH3) {
                    $out .= '<ul>';
                    $openH3 = true;
                }
                $out .= '<li><a href="#' . htmlspecialchars($row[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">' . htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a></li>';
            }
        }
        if ($openH3) {
            $out .= '</ul></li>';
        }
        return $out . '</ul></nav>';
    }

    public function inline(string $markdown): string
    {
        return $this->parser->line($markdown);
    }

    private function shortcodes(string $md): string
    {
        $md = preg_replace_callback('/\{\{<\s*tanim\s+([^>]+)\s*>\}\}(.*?)\{\{<\s*\/tanim\s*>\}\}/us', function ($m) {
            $attrs = $this->attrs($m[1]);
            $inner = $this->parser->text(trim($m[2]));
            $word = htmlspecialchars($attrs['kelime'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $okunus = isset($attrs['okunus']) ? '<span class="definition-pron">/' . htmlspecialchars($attrs['okunus'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '/</span>' : '';
            $koken = isset($attrs['koken']) ? '<span class="chip chip--sm">' . htmlspecialchars($attrs['koken'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</span>' : '';
            return "\n\n<div class=\"definition\"><div class=\"definition-head\"><span class=\"definition-word\">{$word}</span>{$okunus}{$koken}</div><div class=\"definition-body\">{$inner}</div></div>\n\n";
        }, $md) ?? $md;

        $md = preg_replace_callback('/\{\{<\s*bilgi\s+([^>]+)\s*>\}\}(.*?)\{\{<\s*\/bilgi\s*>\}\}/us', function ($m) {
            $attrs = $this->attrs($m[1]);
            $tur = htmlspecialchars($attrs['tur'] ?? 'not', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $baslik = isset($attrs['baslik']) ? '<strong>' . htmlspecialchars($attrs['baslik'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</strong>' : '';
            $inner = $this->parser->text(trim($m[2]));
            return "\n\n<aside class=\"infobox infobox--{$tur}\">{$baslik}{$inner}</aside>\n\n";
        }, $md) ?? $md;

        return $md;
    }

    /** @return array<string, string> */
    private function attrs(string $raw): array
    {
        $out = [];
        if (preg_match_all('/(\w+)="([^"]*)"/u', $raw, $m, PREG_SET_ORDER)) {
            foreach ($m as $row) {
                $out[$row[1]] = $row[2];
            }
        }
        return $out;
    }

    private function tables(string $md): string
    {
        return preg_replace_callback('/(?:^|\n)(\|[^\n]+\|\n\|[-:| ]+\|\n(?:\|[^\n]+\|\n?)*)/u', function ($m) {
            $lines = array_values(array_filter(array_map('trim', explode("\n", trim($m[1])))));
            if (count($lines) < 2) {
                return $m[0];
            }
            $html = "<table>\n<thead><tr>";
            foreach ($this->cells($lines[0]) as $cell) {
                $html .= '<th>' . $this->parser->line($cell) . '</th>';
            }
            $html .= "</tr></thead>\n<tbody>\n";
            foreach (array_slice($lines, 2) as $line) {
                $html .= '<tr>';
                foreach ($this->cells($line) as $cell) {
                    $html .= '<td>' . $this->parser->line($cell) . '</td>';
                }
                $html .= "</tr>\n";
            }
            return "\n" . $html . "</tbody></table>\n";
        }, $md) ?? $md;
    }

    /** @return list<string> */
    private function cells(string $line): array
    {
        $line = trim($line, '|');
        return array_map('trim', explode('|', $line));
    }

    private function headingIds(string $html): string
    {
        return preg_replace_callback('/<h([23])>(.*?)<\/h\1>/us', function ($m) {
            $plain = trim(strip_tags($m[2]));
            $id = Slug::make($plain);
            $anchor = ' <a class="anchor" href="#' . $id . '" aria-label="Bu başlığa bağlantı">#</a>';
            return '<h' . $m[1] . ' id="' . $id . '">' . $m[2] . $anchor . '</h' . $m[1] . '>';
        }, $html) ?? $html;
    }
}
