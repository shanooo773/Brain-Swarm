# Generated by Django 5.2.4 on 2025-07-19 21:43

import django.db.models.deletion
from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('main', '0001_initial'),
    ]

    operations = [
        migrations.AddField(
            model_name='blog',
            name='image',
            field=models.ImageField(blank=True, help_text='Optional featured image for the blog post', null=True, upload_to='blog_images/'),
        ),
        migrations.CreateModel(
            name='Contributor',
            fields=[
                ('id', models.BigAutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('name', models.CharField(help_text="Contributor's full name", max_length=100)),
                ('email', models.EmailField(blank=True, help_text='Optional email address', max_length=254, null=True)),
                ('github', models.URLField(blank=True, help_text='Optional GitHub profile URL', null=True)),
                ('linkedin', models.URLField(blank=True, help_text='Optional LinkedIn profile URL', null=True)),
                ('blog', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, related_name='contributors', to='main.blog')),
            ],
            options={
                'ordering': ['name'],
            },
        ),
    ]
